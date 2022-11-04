<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = [
        'name_room',
        'typ_room',
        'price',
        'capacity',

    ];
    public function client()
    {
        return $this->hasOne(Client::class);
    }
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
}
