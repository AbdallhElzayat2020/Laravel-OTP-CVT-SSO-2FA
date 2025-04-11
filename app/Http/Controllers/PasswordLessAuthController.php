<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordLessAuthController extends Controller
{

    public function store(Request $request)
    {


        // validate
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // check if email exists on db
        $merchant = Merchant::where('email', $request->email)->first();

        // if not exists return error
        if (!$merchant) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // if exists send email with link to Login
        $merchant->sendEmailVerificationNotification();

        // return success message
        return back()->with('status', 'Link sent to your email, please check your Email');
    }

    public function verify()
    {
        
    }
}
