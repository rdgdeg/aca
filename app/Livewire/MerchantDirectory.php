<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Service;
use App\Services\OpeningStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class MerchantDirectory extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $q = '';

    #[Url]
    public ?string $category = null;

    #[Url]
    public string $sort = 'az';

    #[Url]
    public bool $open = false;

    #[Url]
    public bool $sunday = false;

    #[Url]
    public array $services = [];

    public ?int $selected = null;

    public int $perPage = 12;

    public bool $favoritesOnly = false;

    /** @var array<int, int> */
    public array $favoriteIds = [];

    public ?float $userLat = null;

    public ?float $userLng = null;

    public function mount(?string $category = null): void
    {
        if ($category) {
            $this->category = $category;
        }
    }

    public function updated(string $name): void
    {
        if ($name === 'selected' || str_starts_with($name, 'paginators')) {
            return;
        }

        if (! in_array($name, ['perPage', 'userLat', 'userLng', 'favoriteIds'], true)) {
            $this->perPage = 12;
        }

        $this->resetPage();
        $this->dispatch('refreshMap');
    }

    public function paginationView(): string
    {
        return 'pagination.aca';
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
        $this->resetPage();
    }

    public function selectMerchant(int $id): void
    {
        $this->selected = $id;
        $this->dispatch('pulse-pin', id: $id);
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function showFavorites(array $ids): void
    {
        $this->favoriteIds = array_values(array_unique(array_map('intval', $ids)));
        $this->favoritesOnly = true;
        $this->resetPage();
        $this->dispatch('refreshMap');
    }

    public function clearFavorites(): void
    {
        $this->favoritesOnly = false;
        $this->favoriteIds = [];
        $this->resetPage();
        $this->dispatch('refreshMap');
    }

    public function setLocation(float $lat, float $lng): void
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->sort = 'near';
        $this->resetPage();
        $this->dispatch('refreshMap');
    }

    public function clearNear(): void
    {
        $this->userLat = null;
        $this->userLng = null;
        if ($this->sort === 'near') {
            $this->sort = 'az';
        }
        $this->resetPage();
        $this->dispatch('refreshMap');
    }

    public function clearCategory(): void
    {
        $this->category = null;
    }

    public function clearSearch(): void
    {
        $this->q = '';
    }

    public function clearService(string $key): void
    {
        $this->services = array_values(array_filter($this->services, fn ($item) => $item !== $key));
    }

    public function render()
    {
        $opening = app(OpeningStatus::class);
        $query = Merchant::published()->with(['categories', 'openingHours', 'closures', 'services']);

        if ($this->q !== '') {
            $query->search($this->q);
        }
        if ($this->category) {
            $query->whereHas('categories', function ($q) {
                $q->where(function ($q) {
                    $q->where('slug->fr', $this->category)
                        ->orWhere('slug->nl', $this->category)
                        ->orWhere('slug->en', $this->category);
                });
            });
        }
        if ($this->sunday) {
            $query->where(function ($q) {
                $q->where('open_sundays', true)
                    ->orWhereHas('openingHours', fn ($h) => $h->where('weekday', 7));
            });
        }
        if ($this->services) {
            $query->whereHas('services', fn ($q) => $q->whereIn('key', $this->services));
        }
        if ($this->favoritesOnly) {
            $query->whereIn('id', $this->favoriteIds ?: [0]);
        }

        $merchants = $query->get();

        if ($this->open) {
            $merchants = $merchants->filter(fn (Merchant $m) => $opening->for($m)['open']);
        }

        $merchants = match ($this->sort) {
            'new' => $merchants->sortByDesc('member_since'),
            'random' => $merchants->sortBy(fn ($m) => crc32($m->id.'|'.session()->getId())),
            'near' => $merchants->sortBy(function (Merchant $m) {
                if ($this->userLat === null || $this->userLng === null) {
                    return 9999;
                }

                return $m->distanceKm($this->userLat, $this->userLng) ?? 9999;
            }),
            default => $merchants->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE),
        };

        $merchants = $merchants->values();

        if ($this->userLat !== null && $this->userLng !== null) {
            $merchants->each(function (Merchant $merchant) {
                $merchant->near_km = $merchant->distanceKm($this->userLat, $this->userLng);
            });
        }

        $points = $merchants->filter(fn (Merchant $m) => $m->lat && $m->lng)->map(fn (Merchant $m) => [
            'id' => $m->id,
            'name' => $m->name,
            'lat' => $m->lat,
            'lng' => $m->lng,
            'url' => aca_url('merchant', ['slug' => $m->t('slug')]),
            'cover' => $m->cover(),
            'address' => $m->address,
            'color' => $m->categories->first()?->pin_color ?? '#6B2B91',
            'directions' => $m->directionsUrl(),
        ])->values();

        $page = $this->getPage();
        $paginated = new LengthAwarePaginator(
            $merchants->forPage($page, $this->perPage)->values(),
            $merchants->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page'],
        );

        $categories = Category::root()->get();

        return view('livewire.merchant-directory', [
            'merchants' => $paginated,
            'points' => $points,
            'categories' => $categories,
            'activeCategory' => $categories->first(fn (Category $category) => $category->t('slug') === $this->category),
            'allServices' => Service::query()->orderBy('position')->get(),
            'opening' => $opening,
        ])->title(__('Les membres de l’ACA'));
    }
}
