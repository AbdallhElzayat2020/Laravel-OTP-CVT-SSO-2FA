<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantEmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomVerificationTokenController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user('merchant')->hasVerifiedEmail()
            ? redirect()->intended(route('merchant.index', absolute: false))
            : view('merchant.auth.verify-email');
    }

    public function verify(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return redirect()->intended(route('merchant.index', absolute: false) . '?verified=1');
        }

        if ($request->user('merchant')->markEmailAsVerified()) {
            event(new Verified($request->user('merchant')));
        }

        return redirect()->intended(route('merchant.index', absolute: false) . '?verified=1');
    }

    public function resend(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return redirect()->intended(route('merchant.index', absolute: false));
        }

        $request->user('merchant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
