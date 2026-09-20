<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Form;
use App\Models\Merchant;
use App\Models\Page;
use App\Models\Post;
use App\Services\FormProcessor;
use App\Services\OpeningStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SiteController extends Controller
{
    public function home()
    {
        $categories = Category::root()->get();
        $featured = Merchant::published()->with(['categories', 'openingHours', 'closures'])->inRandomOrder()->take(6)->get();
        $spotlight = Event::spotlight();
        $events = Event::upcoming()->with(['type', 'merchant'])->take(8)->get();
        if ($spotlight) {
            $events = $events->reject(fn ($event) => $event->id === $spotlight->id)->prepend($spotlight)->values();
        }
        $posts = Post::published()->news()->take(3)->get();
        $mapMerchants = Merchant::published()->whereNotNull('lat')->whereNotNull('lng')->with('categories')->get();
        $joinForm = Form::system('join');

        return view('pages.home', compact('categories', 'featured', 'events', 'posts', 'mapMerchants', 'joinForm'))->with([
            'title' => __('Accueil'),
        ]);
    }

    public function merchant(string $locale, string $slug)
    {
        $merchant = Merchant::published()->withSlug($slug)->with([
            'categories', 'services', 'openingHours', 'closures', 'socialLinks', 'deals', 'events',
        ])->firstOrFail();

        $similar = Merchant::published()
            ->where('id', '!=', $merchant->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $merchant->categories->pluck('id')))
            ->inRandomOrder()
            ->take(3)
            ->get();

        $status = app(OpeningStatus::class)->for($merchant);
        $hours = $merchant->openingHours->groupBy('weekday');
        $ordered = Merchant::published()->orderBy('name')->get();
        $index = $ordered->search(fn (Merchant $item) => $item->id === $merchant->id);
        $previousMerchant = $index === false || $ordered->count() < 2
            ? null
            : $ordered[($index - 1 + $ordered->count()) % $ordered->count()];
        $nextMerchant = $index === false || $ordered->count() < 2
            ? null
            : $ordered[($index + 1) % $ordered->count()];

        return view('pages.merchant', compact('merchant', 'similar', 'status', 'hours', 'previousMerchant', 'nextMerchant'))->with([
            'title' => $merchant->name,
            'description' => $merchant->t('short_text') ?: $merchant->t('description'),
            'ogImage' => $merchant->cover(),
        ]);
    }

    public function map()
    {
        return redirect()->to(aca_url('merchants').'#carte');
    }

    public function events(Request $request)
    {
        $query = Event::published()->with(['type', 'merchant']);
        $archive = $request->boolean('archives');
        $archive ? $query->past() : $query->upcoming();
        if ($type = $request->string('type')->toString()) {
            $query->whereHas('type', fn ($q) => $q->where('slug', $type));
        }

        $month = Carbon::parse($request->get('month', now()->toDateString()))->startOfMonth();
        $from = $month->copy()->startOfMonth();
        $to = $month->copy()->endOfMonth()->endOfDay();
        $filterMonth = $request->filled('month');

        $monthCounts = (clone $query)->get()
            ->groupBy(fn (Event $event) => $event->starts_at?->format('Y-m'))
            ->filter(fn ($group, $key) => filled($key))
            ->map(fn ($group, $key) => [
                'key' => $key,
                'label' => Carbon::parse($key.'-01')->locale(app()->getLocale())->translatedFormat('F Y'),
                'count' => $group->count(),
            ])
            ->sortKeys()
            ->values();

        if ($filterMonth) {
            $query->where(function ($q) use ($from, $to) {
                $q->whereBetween('starts_at', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNotNull('ends_at')->where('starts_at', '<=', $to)->where('ends_at', '>=', $from);
                    });
            });
        }

        $events = $query->paginate(6)->withQueryString();
        $types = EventType::query()->get();
        $calendarEvents = collect();
        Event::published()
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('starts_at', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNotNull('ends_at')->where('starts_at', '<=', $to)->where('ends_at', '>=', $from);
                    });
            })
            ->orderBy('starts_at')
            ->get()
            ->each(function (Event $event) use ($from, $to, $calendarEvents) {
                $cursor = $event->starts_at->copy()->startOfDay();
                $last = ($event->ends_at ?? $event->starts_at)->copy()->startOfDay();
                if ($cursor->lt($from)) {
                    $cursor = $from->copy()->startOfDay();
                }
                if ($last->gt($to)) {
                    $last = $to->copy()->startOfDay();
                }
                for ($day = $cursor->copy(); $day->lte($last); $day->addDay()) {
                    $key = $day->toDateString();
                    $calendarEvents->put($key, $calendarEvents->get($key, collect())->push($event));
                }
            });

        return view('pages.events', compact('events', 'types', 'archive', 'month', 'calendarEvents', 'monthCounts', 'filterMonth'))
            ->with(['title' => __('Agenda')]);
    }

    public function event(string $locale, string $slug)
    {
        $event = Event::published()->withSlug($slug)->with(['type', 'merchant', 'form.fields'])->firstOrFail();

        return view('pages.event', compact('event'))->with([
            'title' => $event->t('title'),
            'description' => strip_tags($event->t('description')),
            'ogImage' => $event->cover(),
        ]);
    }

    public function eventIcs(string $locale, string $slug)
    {
        $event = Event::published()->withSlug($slug)->firstOrFail();
        $ics = $this->icsEvent($event);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$event->t('slug').'.ics"',
        ]);
    }

    public function calendarFeed()
    {
        $items = Event::upcoming()->get()->map(fn ($e) => $this->icsEvent($e))->implode("\n");
        $body = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//ACA Ath//Agenda//FR\r\n".$items."\r\nEND:VCALENDAR";

        return response($body, 200, ['Content-Type' => 'text/calendar; charset=utf-8']);
    }

    public function deals()
    {
        return redirect()->to(aca_url('home'));
    }

    public function news()
    {
        $posts = Post::published()->news()->paginate(9);

        return view('pages.news', compact('posts'))->with(['title' => __('Actualités')]);
    }

    public function post(string $locale, string $slug)
    {
        $post = Post::published()->withSlug($slug)->firstOrFail();

        return view('pages.post', compact('post'))->with([
            'title' => $post->t('title'),
            'description' => $post->t('excerpt'),
            'ogImage' => $post->cover(),
        ]);
    }

    public function press()
    {
        $releases = Post::published()->press()->get();

        return view('pages.press', compact('releases'))->with(['title' => __('Espace presse')]);
    }

    public function pageByKey(string $locale, string $key)
    {
        $slug = config("aca.routes.{$key}.".aca_locale()) ?? $key;
        $page = Page::published()->withSlug($slug)->first();

        return view('pages.cms', [
            'page' => $page,
            'fallbackKey' => $key,
            'title' => $page?->t('seo_title') ?: $page?->t('title'),
            'description' => $page?->t('seo_description'),
        ]);
    }

    public function cms(string $locale, string $slug)
    {
        $page = Page::published()->withSlug($slug)->firstOrFail();

        return view('pages.cms', [
            'page' => $page,
            'fallbackKey' => null,
            'title' => $page->t('seo_title') ?: $page->t('title'),
            'description' => $page->t('seo_description'),
        ]);
    }

    public function contact()
    {
        $form = Form::system('contact');
        $committee = [
            ['role' => __('Présidence'), 'name' => 'ACA Ath', 'email' => 'president@athinfo.be', 'phone' => '+32 485 92 60 80'],
            ['role' => __('Secrétariat'), 'name' => 'ACA Ath', 'email' => 'secretaire@athinfo.be', 'phone' => null],
        ];

        return view('pages.contact', compact('form', 'committee'))->with(['title' => __('Contact')]);
    }

    public function submitForm(Request $request, FormProcessor $processor, string $locale, Form $form)
    {
        $event = $request->filled('event_id') ? Event::query()->find($request->integer('event_id')) : null;
        $processor->submit($form, $request, $event);

        if ($form->redirect_url) {
            return redirect($form->redirect_url);
        }

        return back()->with('form_success', $form->t('success_message') ?: __('Merci, votre message a bien été envoyé.'));
    }

    public function weekend()
    {
        [$friday, $sunday] = $this->weekendBounds();
        $events = Event::published()
            ->with(['type', 'merchant'])
            ->where(function ($query) use ($friday, $sunday) {
                $query->whereBetween('starts_at', [$friday, $sunday])
                    ->orWhere(function ($query) use ($friday, $sunday) {
                        $query->whereNotNull('ends_at')
                            ->where('starts_at', '<=', $sunday)
                            ->where('ends_at', '>=', $friday);
                    });
            })
            ->orderBy('starts_at')
            ->get();

        $openShops = Merchant::published()
            ->with(['categories', 'openingHours', 'closures'])
            ->where(function ($query) {
                $query->where('open_sundays', true)
                    ->orWhereHas('openingHours', fn ($hours) => $hours->whereIn('weekday', [6, 7]));
            })
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('pages.weekend', compact('events', 'openShops', 'friday', 'sunday'))
            ->with(['title' => __('Week-end à Ath')]);
    }

    public function parking()
    {
        $spots = [
            ['name' => 'Boulevard de Mons', 'hint' => __('Grand parking, accès centre-ville à pied.'), 'lat' => 50.6290, 'lng' => 3.7750],
            ['name' => 'Parking Lina', 'hint' => __('Idéal pour la rue de Bruxelles et les vitrines mode.'), 'lat' => 50.6315, 'lng' => 3.7810],
            ['name' => 'CEVA', 'hint' => __('Proche des halles et de la Grand-Place.'), 'lat' => 50.6275, 'lng' => 3.7765],
            ['name' => 'Tintouille', 'hint' => __('Entrée nord, pratique le samedi matin.'), 'lat' => 50.6328, 'lng' => 3.7740],
            ['name' => 'Sucrerie', 'hint' => __('Capacité généreuse, 10 minutes à pied.'), 'lat' => 50.6350, 'lng' => 3.7700],
            ['name' => 'Locomotives', 'hint' => __('À deux pas de la gare SNCB.'), 'lat' => 50.6270, 'lng' => 3.7860],
            ['name' => 'Pont Carré', 'hint' => __('Stationnement de proximité, côté Dender.'), 'lat' => 50.6295, 'lng' => 3.7825],
        ];

        return view('pages.parking', compact('spots'))->with(['title' => __('Parking & accès')]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function weekendBounds(): array
    {
        $now = now('Europe/Brussels');
        $friday = $now->isoWeekday() >= 5
            ? $now->copy()->startOfWeek(Carbon::MONDAY)->addDays(4)->startOfDay()
            : $now->copy()->next(Carbon::FRIDAY)->startOfDay();
        $sunday = $friday->copy()->addDays(2)->endOfDay();

        return [$friday, $sunday];
    }

    private function icsEvent(Event $event): string
    {
        $uid = $event->id.'@aca-ath.be';
        $stamp = now()->utc()->format('Ymd\THis\Z');
        $start = optional($event->starts_at)?->utc()->format('Ymd\THis\Z');
        $end = optional($event->ends_at)?->utc()->format('Ymd\THis\Z') ?: $start;
        $title = addcslashes($event->t('title'), ',;\\');
        $desc = addcslashes(strip_tags($event->t('description')), ',;\\');
        $loc = addcslashes((string) $event->location, ',;\\');

        return "BEGIN:VEVENT\r\nUID:{$uid}\r\nDTSTAMP:{$stamp}\r\nDTSTART:{$start}\r\nDTEND:{$end}\r\nSUMMARY:{$title}\r\nDESCRIPTION:{$desc}\r\nLOCATION:{$loc}\r\nEND:VEVENT";
    }
}
