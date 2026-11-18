<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UnitConversion extends Model { protected $table='unit_conversions'; protected $guarded=[]; protected $casts=['details'=>'array']; }
