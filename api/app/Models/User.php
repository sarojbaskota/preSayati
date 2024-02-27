<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'fname',
        'lname',
        'username',
        'location',
        'email',
        'password',
        'account_status',
        'online',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }
    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }
    // public function friends()
    // {

    //     $loggedInUserId = auth()->id();


    //     return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
    //     ->where('friends.confirm', 1)
    //     ->where(function ($query) use ($loggedInUserId) {
    //         $query->where('friends.user_id', $loggedInUserId)
    //               ->orWhere('friends.friend_id', $loggedInUserId);
    //     })
    //     ->withTimestamps();
    // }
    public function friendRequests()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
        ->withTimestamps()
        ->where('confirm', 0)
        ->where('sent_by', '!=', auth()->id())
        ->where('friend_id', auth()->id());
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = Str::slug($value).'_'.random_int(0,9999)+random_int(1,9);
    }

    public function getAvatarAttribute($value)
    {
        if (!$value) {
            // Return a default image or placeholder if no avatar is set
            return 'https://mui.com/static/images/avatar/1.jpg';
        }
    
        // Check if the value is already a URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
    
        // Generate the URL to the storage location
        $url = asset("storage/{$value}");
    
        // Replace any occurrences of 'public/' from the path if necessary
        $url = str_replace('public/', '', $url);
    
        return $url;
    }
}
