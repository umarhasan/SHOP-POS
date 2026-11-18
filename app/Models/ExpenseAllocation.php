<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExpenseAllocation extends Model { protected $table='expense_allocations'; protected $guarded=[]; protected $casts=['details'=>'array']; }
