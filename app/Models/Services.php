<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = [
        'name',
        'price',
        'description',
        'status',


    ];

    public function booked()
    {
        return $this->hasMany(Booked::class);
    }
}
