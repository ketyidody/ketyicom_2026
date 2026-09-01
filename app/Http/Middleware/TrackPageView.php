<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /**
     * Path prefixes that should never be tracked (admin, auth, non-visitor pages).
     */
    protected array $skip = [
        'admin',
        'dashboard',
        'profile',
        'login',
        'register',
        'password',
        'email',
        'forgot-password',
        'reset-password',
        'verify-email',
        'confirm-password',
        'cart',
        'checkout',
        'cookie-consent',
        'up',
        'build',
        'storage',
    ];

    /**
     * Pass the request through untouched; the recording happens in terminate()
     * so it runs after the response has been sent to the visitor.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Record the page view after the response has been delivered.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldTrack($request, $response)) {
            return;
        }

        try {
            [$country, $countryCode] = $this->resolveCountry($request->ip());

            PageView::create([
                'session_id' => $request->session()->getId(),
                'url' => '/'.ltrim($request->path(), '/'),
                'referrer' => $this->referrer($request),
                'country' => $country,
                'country_code' => $countryCode,
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Analytics must never break the site — swallow any failure.
            report($e);
        }
    }

    /**
     * Decide whether this request represents a trackable public page view.
     */
    protected function shouldTrack(Request $request, Response $response): bool
    {
        // Track by default (incl. the first visit, before any choice is made).
        // Stop only once the visitor has explicitly declined analytics cookies.
        if ($request->cookie('cookie_consent') === 'declined') {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        // Skip Inertia partial reloads (not real navigations).
        if ($request->headers->get('X-Inertia-Partial-Data')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        $path = $request->path();

        foreach ($this->skip as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the referrer only when it points to an external site.
     */
    protected function referrer(Request $request): ?string
    {
        $referrer = $request->headers->get('referer');

        if (! $referrer) {
            return null;
        }

        // Ignore internal navigation between our own pages.
        if (str_starts_with($referrer, $request->getSchemeAndHttpHost())) {
            return null;
        }

        return substr($referrer, 0, 255);
    }

    /**
     * Resolve [countryName, countryCode] for an IP, cached per IP for 24h.
     */
    protected function resolveCountry(?string $ip): array
    {
        if (! $ip) {
            return [null, null];
        }

        return Cache::remember('geoip:'.$ip, 86400, function () use ($ip) {
            try {
                $position = Location::get($ip);

                if ($position && $position->countryName) {
                    return [$position->countryName, $position->countryCode];
                }
            } catch (\Throwable $e) {
                // Lookup failed (e.g. rate limited) — fall through to unknown.
            }

            return [null, null];
        });
    }
}
