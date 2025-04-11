<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantEmailVerificationRequest;
use App\Models\Merchant;
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

    public function verify(Request $request): RedirectResponse
    {
        $merchant = Merchant::where('verification_token', '=', $request->token)->firstOrFail();

        if (now() < $merchant->verification_token_expires_at) {
            $merchant->VerifyUsingVerificationToken();
            return to_route('merchant.index');
        }

        abort(401);

    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return redirect()->intended(route('merchant.index', absolute: false));
        }

        $request->user('merchant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
