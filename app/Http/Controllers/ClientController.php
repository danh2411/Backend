<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Validator;

class ClientController extends Controller
{

    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'firtname' => 'required|string|between:2,100',
            'lastname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:clients',
            'phone' => 'required|integer|min:10',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Client::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'HTTP Code'=>'200',
            'message' => 'Client successfully registered',
            'user' => $user
        ], 201);
    }

}
