<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ServiceRequest extends Model { protected $fillable=['name','email','service','message','budget','status','admin_notes']; }
