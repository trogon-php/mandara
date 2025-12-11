<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Canonicalize
{
    public function handle(Request $request, Closure $next)
    {
        $preferredHost = config('app.url'); // set to your domain
        $scheme = 'https';

        $currentHost = $request->getHost();
        $currentScheme = $request->getScheme();

        // Extract just the hostname from the domainName if it includes protocol
        if (str_contains($preferredHost, '://')) {
            $preferredHost = parse_url($preferredHost, PHP_URL_HOST);
        }

        // Build canonical URL
        $canonicalUrl = $scheme . '://' . $preferredHost . $request->getRequestUri();

        if ($currentHost !== $preferredHost || $currentScheme !== $scheme) {
            return redirect()->to($canonicalUrl, 301);
        }

        return $next($request);
    }
}
