<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model { protected $guarded=[]; protected $casts=['attributes'=>'array','track_stock'=>'boolean','is_active'=>'boolean']; public function category(){return $this->belongsTo(Category::class);} public function businessUnit(){return $this->belongsTo(BusinessUnit::class);} public function baseUnit(){return $this->belongsTo(Unit::class,'base_unit_id');} public function variants(){return $this->hasMany(ProductVariant::class);} public function stock(){return $this->hasOne(StockBalance::class); } public function stockMovements(){return $this->hasMany(\App\Models\StockMovement::class);} }
