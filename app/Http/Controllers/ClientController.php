<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Http\Request;
use Validator;

class ClientController extends Controller
{
    public function __construct() {
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
    {     $user=Client::query()->where->get();
        dd($user);
        $validator = Validator::make($request->all(), [
            'firtname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|max:100|unique:clients',
            'phone' => 'required|integer|min:10',
            'status' => 'required|integer|min:0|max:1',
            'CMD' => 'required|integer|min:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


            Client::where('id', $id)->update(
            ['firtname' => $request->firtname,
            'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
                'CMD' => $request->CMND,

            ]
        );

        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Client successfully changed profile',
            'client' => $id,
        ], 201);
    }

    public function hiden(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Client::where('id', $id)->update(
            ['status' => $request->status]
        );
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Hiden client successfully ',
            'client' => $id,
        ], 201);
    }
    public function clientProfile(Request $request) {
        $user = Client::where('id',$request->id)->get();
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Hiden client successfully ',
            'data' => $user,
        ], 201);
    }

}
