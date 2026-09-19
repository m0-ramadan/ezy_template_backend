<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ResourceFile extends Model { protected $fillable=['resource_id','label','path','original_name','extension','mime_type','size_bytes','quality','format','is_primary','sort_order']; protected $casts=['is_primary'=>'boolean']; public function resource(){return $this->belongsTo(Resource::class);}
 public function views(){return $this->hasMany(FileView::class);}
 public function downloads(){return $this->hasMany(Download::class,'resource_file_id');} public function getSizeHumanAttribute(){ $s=$this->size_bytes; foreach(['B','KB','MB','GB'] as $u){if($s<1024)return round($s,1).' '.$u;$s/=1024;}return round($s,1).' TB';} }
