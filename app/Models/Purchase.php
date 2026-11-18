<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class Purchase extends Model {protected $guarded=[];protected $casts=['purchased_at'=>'datetime'];public function items(){return $this->hasMany(PurchaseItem::class);}public function supplier(){return $this->belongsTo(Contact::class,'supplier_id');}}
