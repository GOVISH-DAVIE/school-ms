<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FeeStructureItem extends Model {
    protected $table='fee_structure_items'; protected $guarded=[];
    public function head(){ return $this->belongsTo(FeeHead::class,'fee_head_id'); }
}
