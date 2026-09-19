<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Download extends Model { protected $fillable=['resource_id','resource_file_id','user_id','ip_address','user_agent','quality','download_token','visitor_hash']; public function resource(){return $this->belongsTo(Resource::class);} public function file(){return $this->belongsTo(ResourceFile::class,'resource_file_id');}
 public function user(){return $this->belongsTo(User::class);} }
