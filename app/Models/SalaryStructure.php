<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalaryStructure extends Model { protected $table='salary_structures'; protected $guarded=[];
  protected $casts=['allowances'=>'array','deductions'=>'array']; }
