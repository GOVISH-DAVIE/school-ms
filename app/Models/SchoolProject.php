<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolProject extends Model { protected $table='school_projects'; protected $guarded=[];
  public function transactions(){ return $this->hasMany(ProjectTransaction::class,'project_id'); } }
