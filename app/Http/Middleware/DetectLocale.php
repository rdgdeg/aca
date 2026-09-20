<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('storage*')) {
            return $next($request);
        }

        if ($request->segment(1) && in_array($request->segment(1), aca_locales(), true)) {
            return $next($request);
        }

        if ($request->path() === '/' || $request->path() === '') {
            $locale = $request->session()->get('locale');
            if (! $locale && ! $request->session()->get('locale_prompted')) {
                $preferred = substr((string) $request->getPreferredLanguage(['fr', 'nl', 'en']), 0, 2);
                $locale = in_array($preferred, aca_locales(), true) ? $preferred : 'fr';
                $request->session()->put('locale_prompted', true);
            }
            $locale = $locale && in_array($locale, aca_locales(), true) ? $locale : 'fr';

            return redirect('/'.$locale);
        }

        return $next($request);
    }
}
