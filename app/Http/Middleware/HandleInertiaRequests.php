<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $sessionId = $request->session()->getId();
        $cartCount = CartItem::where('session_id', $sessionId)->count();

        $baseData =  [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];

        return array_merge($baseData, [
            'announcements' => Setting::get('homepage.announcement', ''),
            'shopctaTitle' => Setting::get('homepage.shopcta.title', ''),
            'shopctaDesc' => Setting::get('homepage.shopcta.desc', ''),
            'cartCount' => $cartCount,
        ]);
    }
}
