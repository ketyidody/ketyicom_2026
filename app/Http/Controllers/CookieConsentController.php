<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    /**
     * Store the visitor's cookie/analytics consent choice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'consent' => 'required|in:accepted,declined',
        ]);

        // Forever cookie (~5 years) so the banner stays dismissed. Left
        // unencrypted (see bootstrap/app.php) so the tracking middleware and
        // client can both read the plain value.
        return back()->withCookie(
            cookie()->forever('cookie_consent', $validated['consent'])
        );
    }
}
