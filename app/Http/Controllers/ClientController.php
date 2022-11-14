<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
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
            'firtname' => 'required|string|between:2,100',
            'lastname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:clients',
            'phone' => 'required|string|min:10',
            'CMND/CCCD' => 'required|string|min:10',
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
                'firtname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|string|email|max:100|unique:clients,email,' . $id,
                'phone' => 'required|integer|min:10',

                'CMND' => 'required|string|max:15',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            Client::query()->where('id', $id)->update(
                ['firtname' => $request->firtname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'CMND/CCCD' => $request->CMND,
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
        }else{
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'client' => $id,
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
       if (!empty($request->id)){
           $user = Client::where('id', $request->id)->get();
           if (!empty($user)){
               $arr['message']='Find successful client: '.$request->id;
           }else{
               $arr['message']='No client found: '.$request->id;
           }
       }else{
           $user = Client::all();
           $arr['message']='All clients';
       }
        $arr[ 'HTTP Code']='200';
        $arr[ 'data']=$user;
        return response()->json($arr, 201);
    }

}
