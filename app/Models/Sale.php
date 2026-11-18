<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model { protected $guarded=[]; protected $casts=['sold_at'=>'datetime']; public function items(){return $this->hasMany(SaleItem::class);} public function customer(){return $this->belongsTo(Contact::class,'customer_id');} }
