<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Merchant;
use App\Models\Post;
use Illuminate\Support\Str;

if (! function_exists('aca_locales')) {
    function aca_locales(): array
    {
        return array_keys(config('aca.locales'));
    }
}

if (! function_exists('aca_locale')) {
    function aca_locale(): string
    {
        $locale = app()->getLocale();

        return in_array($locale, aca_locales(), true) ? $locale : 'fr';
    }
}

if (! function_exists('aca_path')) {
    function aca_path(string $name, array $params = [], ?string $locale = null): string
    {
        $locale ??= aca_locale();
        $segment = config("aca.routes.{$name}.{$locale}", config("aca.routes.{$name}.fr"));

        $extra = '';
        if (isset($params['slug'])) {
            $extra = '/'.$params['slug'];
        }
        if (isset($params['category'])) {
            $extra = '/'.$params['category'];
        }

        $path = '/'.$locale.($segment === '/' ? '' : '/'.$segment).$extra;

        return rtrim($path, '/') ?: '/'.$locale;
    }
}

if (! function_exists('aca_url')) {
    function aca_url(string $name, array $params = [], ?string $locale = null): string
    {
        return url(aca_path($name, $params, $locale));
    }
}

if (! function_exists('aca_normalize')) {
    function aca_normalize(?string $value): string
    {
        $value = Str::of((string) $value)->ascii()->lower()->value();

        return preg_replace('/[^a-z0-9]+/i', ' ', $value) ?: '';
    }
}

if (! function_exists('aca_switch_locale')) {
    function aca_switch_locale(string $target): string
    {
        $current = aca_locale();
        $parts = explode('/', trim(request()->path(), '/'));
        array_shift($parts);
        $segment = $parts[0] ?? null;
        $extra = $parts[1] ?? null;

        $routeName = 'home';
        if ($segment) {
            foreach (config('aca.routes') as $name => $map) {
                if (in_array($segment, array_values($map), true)) {
                    $routeName = $name;
                    break;
                }
            }
        }

        if ($extra && $routeName === 'events') {
            $routeName = 'event';
        }
        if ($extra && $routeName === 'news') {
            $routeName = 'post';
        }

        $params = [];
        if ($extra && in_array($routeName, ['merchant', 'event', 'post'], true)) {
            $model = match ($routeName) {
                'merchant' => Merchant::query()->withSlug($extra)->first(),
                'event' => Event::query()->withSlug($extra)->first(),
                'post' => Post::query()->withSlug($extra)->first(),
            };
            $params['slug'] = $model?->getTranslation('slug', $target, false) ?: ($model?->t('slug') ?: $extra);
        } elseif ($extra && $routeName === 'merchants') {
            $category = Category::query()->where(function ($q) use ($extra) {
                $q->where('slug->fr', $extra)->orWhere('slug->nl', $extra)->orWhere('slug->en', $extra);
            })->first();
            $params['category'] = $category?->getTranslation('slug', $target, false) ?: ($category?->t('slug') ?: $extra);
        }

        $url = aca_url($routeName, $params, $target);

        $query = request()->query();
        if (isset($query['category'])) {
            $category = Category::query()->where(function ($q) use ($query) {
                $q->where('slug->fr', $query['category'])
                    ->orWhere('slug->nl', $query['category'])
                    ->orWhere('slug->en', $query['category']);
            })->first();
            if ($category) {
                $query['category'] = $category->getTranslation('slug', $target, false) ?: $category->t('slug');
            }
        }
        unset($query['locale']);

        return $query === [] ? $url : $url.'?'.http_build_query($query);
    }
}

if (! function_exists('aca_hreflangs')) {
    function aca_hreflangs(): array
    {
        $links = [];
        foreach (aca_locales() as $locale) {
            $links[$locale] = aca_switch_locale($locale);
        }
        $links['x-default'] = $links['fr'] ?? reset($links);

        return $links;
    }
}
