<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class Expense extends Model {protected $guarded=[];protected $casts=['expense_date'=>'date'];public function allocations(){return $this->hasMany(ExpenseAllocation::class);}}
