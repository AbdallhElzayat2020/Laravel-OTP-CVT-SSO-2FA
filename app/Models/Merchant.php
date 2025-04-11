<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\MerchantEmailVerification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Merchant extends Authenticatable implements MustVerifyEmail
{


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable;


    // custom Notification for Merchant
    public function sendEmailVerificationNotification()
    {
        if (config('verification.way') === 'email') {

            $url = URL::temporarySignedRoute(
                'merchant.verification.verify', now()->addMinutes(30),
                [
                    'id' => $this->getKey(), //  $this->>id
                    'hash' => sha1($this->getEmailForVerification()), // $this->email
                ]
            );

            $this->notify(new MerchantEmailVerification($url));
        }

        if (config('verification.way') === 'cvt') {
            $this->generateVerificationToken();
            $url = route('merchant.verification.verify', [
                'id' => $this->getKey(),
                'token' => $this->verification_token,
            ]);

            $this->notify(new MerchantEmailVerification($url));
        }
    }

    /* Custom  Verification token */

    public function generateVerificationToken()
    {
        if (config('verification.way') === 'cvt') {
            $this->verification_token = Str::random(40);
            $this->verification_token_expires_at = now()->addMinutes(1);
            $this->save();
        }
    }

    public function VerifyUsingVerificationToken()
    {
        if (config('verification.way') === 'cvt') {
            $this->email_verified_at = now();
            $this->verification_token = null;
            $this->verification_token_expires_at = null;
            $this->save();
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
