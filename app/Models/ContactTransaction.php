<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContactTransaction extends Model { protected $table='contact_transactions'; protected $guarded=[]; protected $casts=['details'=>'array']; }
