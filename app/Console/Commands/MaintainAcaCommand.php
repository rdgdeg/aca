<?php

namespace App\Console\Commands;

use App\Enums\PublishStatus;
use App\Enums\SubmissionStatus;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Merchant;
use App\Models\Page;
use App\Models\Post;
use App\Models\Submission;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class MaintainAcaCommand extends Command
{
    protected $signature = 'aca:maintain';

    protected $description = 'Archive events, expire deals, purge old submissions, rebuild sitemap';

    public function handle(): int
    {
        Event::query()->where('status', PublishStatus::Published)
            ->where('starts_at', '<', now()->subDay())
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '<', now());
            })->update(['status' => PublishStatus::Archived]);

        Deal::query()->where('status', PublishStatus::Published)
            ->whereDate('ends_on', '<', now()->toDateString())
            ->update(['status' => PublishStatus::Archived]);

        Submission::query()->where('created_at', '<', now()->subMonths(24))
            ->where('status', SubmissionStatus::Archived)
            ->delete();

        $this->writeSitemap();

        $this->info('ACA maintenance done.');

        return self::SUCCESS;
    }

    private function writeSitemap(): void
    {
        $sitemap = Sitemap::create();
        $statics = ['home', 'merchants', 'map', 'events', 'news', 'press', 'join', 'contact', 'legal', 'privacy', 'cookies'];

        foreach (aca_locales() as $locale) {
            foreach ($statics as $name) {
                $sitemap->add(Url::create(aca_url($name, [], $locale)));
            }
        }

        Merchant::published()->get()->each(function (Merchant $merchant) use ($sitemap) {
            foreach (aca_locales() as $locale) {
                $sitemap->add(Url::create(aca_url('merchant', ['slug' => $merchant->t('slug', $locale) ?: $merchant->t('slug')], $locale)));
            }
        });

        Event::published()->get()->each(function (Event $event) use ($sitemap) {
            foreach (aca_locales() as $locale) {
                $sitemap->add(Url::create(aca_url('event', ['slug' => $event->t('slug', $locale) ?: $event->t('slug')], $locale)));
            }
        });

        Post::published()->get()->each(function (Post $post) use ($sitemap) {
            foreach (aca_locales() as $locale) {
                $sitemap->add(Url::create(aca_url('post', ['slug' => $post->t('slug', $locale) ?: $post->t('slug')], $locale)));
            }
        });

        Page::published()->get()->each(function (Page $page) use ($sitemap) {
            $slugs = collect(aca_locales())->map(fn (string $locale) => $page->t('slug', $locale) ?: $page->t('slug'));
            if ($slugs->contains(fn (?string $slug) => in_array($slug, ['association', 'vereniging'], true))) {
                return;
            }
            foreach (aca_locales() as $locale) {
                $sitemap->add(Url::create(url('/'.$locale.'/'.($page->t('slug', $locale) ?: $page->t('slug')))));
            }
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
