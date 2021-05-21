<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\UserType;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'username',
    'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  public $timestamps = false;

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'join_date' => 'datetime',
  ];

  /**
   * Hash password before save user
   */
  public function setPasswordAttribute($value)
  {
    $this->attributes['password'] = Hash::make($value);
  }

  public static function get_username($user_id)
  {
    return User::select('username')->where('id', $user_id)->value('username');
  }

  public static function generate_gravatar($user_id)
  {
    $user = User::findOrFail($user_id);
    return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($user->email)));
  }

  public function websites()
  {
    return $this->hasMany(Website::class, 'user_id')->orderBy('id', 'desc');
  }

  public function active_websites()
  {
    return $this->hasMany(Website::class, 'user_id')->where('status', '!=', 'Suspended');
  }

  public function banners()
  {
    return $this->hasMany(Banner::class, 'user_id')->orderBy('id', 'desc');
  }

  public function active_banners()
  {
    return $this->hasMany(Banner::class, 'user_id')->where('status', '!=', 'Suspended');
  }

  public function texts()
  {
    return $this->hasMany(TextAd::class, 'user_id')->orderBy('id', 'desc');
  }

  public function active_texts()
  {
    return $this->hasMany(TextAd::class, 'user_id')->where('status', '!=', 'Suspended');
  }

  public function type()
  {
    return $this->hasOne(UserType::class, 'id', 'user_type');
  }

  public function active_surf_codes()
  {
    return $this->hasMany(SurfCodeClaim::class, 'user_id')->where('completed', 0);
  }

  public function completed_surf_codes()
  {
    return $this->hasMany(SurfCodeClaim::class, 'user_id')->where('completed', 1);
  }

  public function referrals()
  {
    return $this->hasMany(User::class, 'upline', 'id');
  }

  public function start_pages()
  {
    return $this->hasMany(StartPage::class, 'user_id');
  }

  public function getTotalSurfedAttribute()
  {
    return $this->correct_clicks + $this->wrong_clicks;
  }
}
