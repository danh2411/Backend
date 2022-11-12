<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $table = 'bills';
    protected $fillable = [
        'account_id',
        'received_date',
        'payday',
        'total_room_rate',
        'total_service_fee',
        'total_money',
        'status'

    ];
    public function client()
    {
        return $this->hasOne(Client::class);
    }
    public function booked()
    {
        return $this->hasMany(Booked::class);
    }
    public function room()
    {
        return $this->hasMany(Room::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
