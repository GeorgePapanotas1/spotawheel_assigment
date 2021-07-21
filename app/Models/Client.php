<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'surname'
    ];

    public function payments(){
        return $this->hasMany(Payments::class,'user_id', 'id');
    }

    public function limitPayments(){
        return $this->hasMany(Payments::class,'user_id', 'id')->limit(1);
    }

}
