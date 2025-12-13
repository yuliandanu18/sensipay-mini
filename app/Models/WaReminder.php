<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaReminder extends Model
{
    protected $fillable = [
        'invoice_id','parent_id','target','message','status','provider',
        'provider_message_id','reference','last_payload','sent_at','delivered_at','failed_at'
    ];

    protected $casts = [
        'last_payload'=>'array',
        'sent_at'=>'datetime',
        'delivered_at'=>'datetime',
        'failed_at'=>'datetime',
    ];

    public function invoice(){ return $this->belongsTo(Invoice::class); }
    public function parent(){ return $this->belongsTo(ParentModel::class,'parent_id'); }
}