<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function store(Request $request)
    {
        // validate email
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
        $merchant->generateOtp();

        // send OTP to SMS using Provider


        // return success message
        return view('merchant.auth.verifyOtp', ['email' => $request->email]);
    }


    public function verify(Request $request)
    {
        // validate email
//        $request->validate([
//            'email' => ['required', 'email', 'max:255'],
//        ]);
//
//        // check if email exists on db
//        $merchant = Merchant::where('email', $request->email)->first();
//
//        // if not exists return error
//        if (!$merchant) {
//            throw ValidationException::withMessages([
//                'email' => trans('auth.failed'),
//            ]);
//        }
//
//        // if exists send email with link to Login
//        $merchant->generateOtp();
//
//        // send OTP to SMS using Provider
//
//
//        // return success message
//        return back()->with('status', 'Link sent to your email, please check your Email');
    }


}
