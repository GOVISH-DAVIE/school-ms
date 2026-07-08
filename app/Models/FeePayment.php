<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FeePayment extends Model {
    protected $table='fee_payments'; protected $guarded=[];
    public function invoice(){ return $this->belongsTo(Invoice::class,'invoice_id'); }
    public function student(){ return $this->belongsTo(User::class,'student_id'); }
}
