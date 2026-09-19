<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable { use Notifiable; protected $fillable=['name','email','password','role','status']; protected $hidden=['password','remember_token']; protected function casts(): array{return ['password'=>'hashed','email_verified_at'=>'datetime'];} public function downloads(){return $this->hasMany(Download::class);} }
