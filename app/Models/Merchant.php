<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\MerchantEmailVerification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class Merchant extends Authenticatable implements MustVerifyEmail
{


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable;


    // custom Notification for Merchant
    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'merchant.verification.verify', now()->addMinutes(30),
            [
                'id' => $this->getKey(), //  $this->>id
                'hash' => sha1($this->getEmailForVerification()), // $this->email
            ]
        );

        $this->notify(new MerchantEmailVerification($url));
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
