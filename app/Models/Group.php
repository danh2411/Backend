<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'group-accounts';
    protected $fillable = [
        'name',

    ];
    public function account()
    {
        return $this->hasMany(Account::class);
    }
}
