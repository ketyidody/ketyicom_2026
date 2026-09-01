<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    /**
     * Show the traffic statistics dashboard.
     */
    public function index(Request $request)
    {
        // Selected window in days (default 30).
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 30, 90, 365], true) ? $range : 30;

        $since = Carbon::now()->subDays($range - 1)->startOfDay();

        $windowed = PageView::where('created_at', '>=', $since);

        return Inertia::render('Admin/Statistics/Index', [
            'filters' => ['range' => $range],
            'totals' => $this->totals(),
            'topCountries' => $this->topCountries($since),
            'topPages' => $this->topPages($since),
            'topReferrers' => $this->topReferrers($since),
            'viewsPerDay' => $this->viewsPerDay($since, $range),
            'recentVisitors' => $this->recentVisitors(),
        ]);
    }

    /**
     * Headline counters (all-time + rolling windows).
     */
    protected function totals(): array
    {
        $today = Carbon::now()->startOfDay();

        return [
            'totalViews' => PageView::count(),
            'uniqueVisitors' => PageView::distinct('session_id')->count('session_id'),
            'viewsToday' => PageView::where('created_at', '>=', $today)->count(),
            'views7d' => PageView::where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())->count(),
            'views30d' => PageView::where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())->count(),
        ];
    }

    /**
     * Visits grouped by country within the window.
     */
    protected function topCountries(Carbon $since): array
    {
        $rows = PageView::where('created_at', '>=', $since)
            ->select('country', 'country_code', DB::raw('count(*) as views'))
            ->groupBy('country', 'country_code')
            ->orderByDesc('views')
            ->limit(12)
            ->get();

        $total = $rows->sum('views');

        return $rows->map(fn ($row) => [
            'country' => $row->country ?: 'Unknown',
            'country_code' => $row->country_code,
            'views' => (int) $row->views,
            'percent' => $total > 0 ? round($row->views / $total * 100, 1) : 0,
        ])->all();
    }

    /**
     * Most visited pages within the window.
     */
    protected function topPages(Carbon $since): array
    {
        return PageView::where('created_at', '>=', $since)
            ->select('url', DB::raw('count(*) as views'))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->url,
                'views' => (int) $row->views,
            ])->all();
    }

    /**
     * External referrers within the window.
     */
    protected function topReferrers(Carbon $since): array
    {
        return PageView::where('created_at', '>=', $since)
            ->whereNotNull('referrer')
            ->select('referrer', DB::raw('count(*) as views'))
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'referrer' => $row->referrer,
                'views' => (int) $row->views,
            ])->all();
    }

    /**
     * Daily view counts across the window (zero-filled for missing days).
     */
    protected function viewsPerDay(Carbon $since, int $range): array
    {
        $counts = PageView::where('created_at', '>=', $since)
            ->select(DB::raw('date(created_at) as day'), DB::raw('count(*) as views'))
            ->groupBy('day')
            ->pluck('views', 'day');

        $series = [];
        for ($i = 0; $i < $range; $i++) {
            $date = $since->copy()->addDays($i);
            $key = $date->toDateString();
            $series[] = [
                'date' => $key,
                'label' => $date->format('M j'),
                'views' => (int) ($counts[$key] ?? 0),
            ];
        }

        return $series;
    }

    /**
     * Latest visitor sessions with their ordered page journey.
     */
    protected function recentVisitors(): array
    {
        // Most recently active sessions.
        $sessions = PageView::select('session_id', DB::raw('max(created_at) as last_seen'))
            ->groupBy('session_id')
            ->orderByDesc('last_seen')
            ->limit(15)
            ->get();

        return $sessions->map(function ($session) {
            $views = PageView::where('session_id', $session->session_id)
                ->orderBy('created_at')
                ->get(['url', 'country', 'country_code', 'referrer', 'created_at']);

            return [
                'session_id' => substr($session->session_id, 0, 8),
                'last_seen' => $session->last_seen,
                'country' => optional($views->first())->country ?: 'Unknown',
                'country_code' => optional($views->first())->country_code,
                'referrer' => optional($views->first())->referrer,
                'page_count' => $views->count(),
                'pages' => $views->map(fn ($v) => [
                    'url' => $v->url,
                    'at' => $v->created_at,
                ])->all(),
            ];
        })->all();
    }
}
