<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'firtname',
        'lastname',
        'email',
        'phone',
        'status',
        'CCCD',
    ];
    public function bill()
    {
        return $this->hasMany(Bill::class);
    }
    public function booked()
    {
        return $this->hasMany(Booked::class);
    }
    public function room()
    {
        return $this->hasMany(Room::class);
    }
}
