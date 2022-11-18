<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GoupController extends Controller
{ public function __construct()
{
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
}
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Group::create(array_merge(
            $validator->validated(),

        ));
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Client successfully registered',
            'client' => $user
        ], 201);
    }
}
