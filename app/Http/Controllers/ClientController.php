<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bill;
use App\Models\Client;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firtname' => 'required|string|between:2,100|not_regex:/^.+@.+$/i',
            'lastname' => 'required|string|between:2,100|not_regex:/^.+@.+$/i',
            'email' => 'required|string|email|max:100|unique:clients',
            'phone' => 'required|string|min:10|unique:clients',
            'CCCD' => 'required|string|max:13|unique:clients',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Client::create(
            $validator->validated());
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Client successfully registered',
            'client' => $user
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $client = Client::query()->find($id);
        if (!empty($client)) {
            $validator = Validator::make($request->all(), [
                'firtname' => 'required|string|not_regex:/^.+@.+$/i',
                'lastname' => 'required|string|not_regex:/^.+@.+$/i',
                'email' => 'required|string|email|max:100|unique:clients,email,' . $id,
                'phone' => 'required|string|min:10|unique:clients,phone,' . $id,
                'CCCD' => 'required|string|max:13|unique:clients,CCCD,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            Client::query()->where('id', $id)->update(
                ['firtname' => $request->firtname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'CCCD' => $request->CCCD,
                ]
            );
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Client successfully changed profile',
                'client' => $id,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Client:' . $id . ' not found',

            ];
        }
        return response()->json($arr, 201);
    }

    public function hiden(Request $request, $id)
    {
        $user = Client::query()->where('id', $id)->first();

        if (!empty($user)) {
            $status = $user->status === 1 ? 0 : 1;
            $user = Client::where('id', $id)->update(
                ['status' => $status]
            );
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Client status change successful ',
                'client' => $id,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'client' => $id,
            ];
        }

        return response()->json($arr, 201);
    }
    public  function getClient(Request $request){
        $ser=Client::query()->get();
        if (!empty($ser)){
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'data' => $ser,
            ];
        }else{
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'data' => $ser,
            ];
        }
        return response()->json($arr, 201);
    }

    public function clientProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if (!empty($request->id)) {
            $user = Client::where('id', $request->id)->get();
            if (!empty($user->id)) {
                $arr['message'] = 'Find successful client: ' . $request->id;
            } else {
                $arr['message'] = 'No client found: ' . $request->id;
            }
        } else {

            $query = Client::query();
            $perpage = $request->input('perpage', 9);
            $page = $request->input('page', 1);
            $total = $query->count();
            $user = $query->offset(($page - 1) * $perpage)->limit($perpage)->get();

            $arr['message'] = 'All clients';
        }
        $arr['HTTP Code'] = '200';
        $arr['data'] = $user;
        return response()->json($arr, 201);
    }
 public  function  searchClient(Request  $request){

   if (!empty($request->key)){
       if (is_numeric($request->key)){
           $client=null;

           if (strlen($request->key)==13){
               $client=Client::query()->where('CCCD',$request->key)->get();
           }elseif(strlen($request->key)==10){
               $client=Client::query()->where('phone',$request->key)->get();
           }else{
               $arr['message']='Kh??ng t??m th???y kh??ch h??ng. H??y nh???p SDT ho???c CCCD ????? t??m';
           }
           $arr['HTTP Code'] = '200';
           $arr['data'] = $client;
           return response()->json($arr, 201);

       }else{
           $arr['message']=' H??y nh???p SDT ho???c CCCD ????? t??m';
           $arr['HTTP Code'] = '200';
           $arr['data'] = null;
           return response()->json($arr, 201);
       }
   }else{
       $client=Client::all()??null;
       $arr['HTTP Code'] = '200';
       $arr['data'] = $client;
       return response()->json($arr, 201);
   }
 }
}
