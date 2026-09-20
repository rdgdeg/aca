<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class LegacyRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('legacy_redirects')) {
            return $next($request);
        }

        $path = '/'.ltrim($request->getPathInfo(), '/');
        $to = DB::table('legacy_redirects')
            ->where(function ($query) use ($path) {
                $query->where('from_path', $path)->orWhere('from_path', ltrim($path, '/'));
            })
            ->value('to_path');

        if ($to) {
            return redirect($to, 301);
        }

        return $next($request);
    }
}
