<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Invoice extends Model {
    protected $table='invoices'; protected $guarded=[];
    public function items(){ return $this->hasMany(InvoiceItem::class,'invoice_id'); }
    public function payments(){ return $this->hasMany(FeePayment::class,'invoice_id'); }
    public function student(){ return $this->belongsTo(User::class,'student_id'); }
}
