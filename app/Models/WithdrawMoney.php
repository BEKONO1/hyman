<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawMoney extends Model
{
    use HasFactory;
    protected $table = 'withdraw_money';
    protected $fillable = ['user_id','amount','bank_id','datetime','payment_type','transaction','withdraw_money_id','status'];
    protected $casts = [
        'bank_id' => 'integer',
        'user_id'   => 'integer',
        'amount'   => 'double',
    ];

    public function providers(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id','id');
    }

}
