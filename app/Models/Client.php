<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'firtname',
        'lastname',
        'email',
        'phone',
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
