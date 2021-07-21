<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'amount'
    ];

    public function user(){
        return $this->belongsTo(Client::class,'user_id', 'id');
    }
}
