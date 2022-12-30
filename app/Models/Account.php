<?php

namespace App\Models;
namespace App\Models;
use App\Mail\SendCodeMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Account extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function group_account()
    {
        return $this->hasOne(Group::class);
    }
    public function bill()
    {
        return $this->hasMany(Bill::class);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [$this->name];
    }
    public function generateCode()
    {

        $code = rand(1000, 9999);



        UserCode::updateOrCreate(

            [ 'user_id' => auth()->user()->id ],

            [ 'code' => $code ]

        );



        try {



            $details = [

                'title' => 'Tuyệt đối không chia sẽ mã này cho người khác. Mã xác nhận của bạn là: '. $code,



            ];



            Mail::to(auth()->user()->email)->send(new SendCodeMail($details));



        } catch (Exception $e) {

            info("Error: ". $e->getMessage());

        }

    }
}
