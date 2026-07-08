<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payslip extends Model { protected $table='payslips'; protected $guarded=[];
  protected $casts=['allowances'=>'array','deductions'=>'array']; }
