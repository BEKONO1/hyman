<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderSubscription extends Model
{
    use HasFactory;
    protected $table = 'provider_subscriptions';
    protected $fillable = [
        'plan_id', 'user_id', 'title','identifier', 'type','start_at','end_at','amount','status','payment_id','plan_limitation','active_in_app_purchase_identifier','duration','description','plan_type'
    ];
    protected $casts = [
        'amount'    => 'double',
        'user_id' => 'integer',
        'plan_id' => 'integer',
        'payment_id' => 'integer',
    ];
    public function payment(){
        return $this->belongsTo(SubscriptionTransaction::class, 'subscription_plan_id','id');
    }
   
    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
