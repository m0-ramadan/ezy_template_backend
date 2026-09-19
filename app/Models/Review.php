<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Review extends Model { protected $fillable=['resource_id','user_id','name','email','rating','body','status']; protected $casts=['rating'=>'integer']; public function resource(){return $this->belongsTo(Resource::class);} public function user(){return $this->belongsTo(User::class);} }
