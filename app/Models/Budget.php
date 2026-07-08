<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Budget extends Model { protected $table='budgets'; protected $guarded=[];
  public function items(){ return $this->hasMany(BudgetItem::class,'budget_id'); } }
