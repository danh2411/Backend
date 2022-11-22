<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booked extends Model
{
    use HasFactory;
    protected $table = 'booked-services';
    protected $fillable = [

        'client_id',
        'services_id',
        'amount',
        'bill_id',

    ];
    public function service()
    {
        return $this->hasMany(Services::class);
    }
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
    public function client()
    {
        return $this->hasOne(\http\Client::class);
    }

}
