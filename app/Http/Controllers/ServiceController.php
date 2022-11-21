<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;
use Validator;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    //create
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string|unique:services',
            'price' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $service = Services::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => "Created service successfully ",
            'data' => $service
        ];
        return response()->json($arr, 201);
    }


    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string|unique:services,name,' . $id,
            'price' => 'required|numeric|min:0',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $service = Services::query()->find($id);
        if (!empty($service)) {
            Services::where('id', $id)->update(
                [
                    'name' => $request->name,
                    'price' => $request->price,
                    'description'=>$request->description,
                ]
            );
            return response()->json([
                'HTTP Code' => 200,
                'message' => 'The service was successfully updated',
                'service' => $id
            ], 201);
        } else {
            return response()->json([
                'HTTP Code' => 200,
                'message' => 'The account update failed.Servies not found',
                'service' => $id
            ], 201);
        }

    }


    //hiden
    public function hiden(Request $request, $id)
    {
        $user = Services::query()->where('id', $id)->first();

        if (!empty($user)) {
            $status = $user->status === 1 ? 0 : 1;
            $user = Services::where('id', $id)->update(
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


    //serviceInfo
    public function serviceInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'numeric',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ]);
        $total = 1;
        $page = 1;
        $perpage = 1;
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if (!empty($request->id)) {
            $user = Services::where('id', $request->id)->get();

            if (!empty($user)) {
                $arr['message'] = 'Find successful client: ' . $request->id;
            } else {
                $arr['message'] = 'No client found: ' . $request->id;
            }
        } else {


            $query = Services::query();
            $perpage = $request->input('perpage', 9);
            $page = $request->input('page', 1);
            $total = $query->count();
            $user = $query->offset(($page - 1) * $perpage)->limit($perpage)->get();


            $arr['message'] = 'All Services';

        }
        $arr['HTTP Code'] = '200';

        $arr['total'] = $total;
        $arr['page'] = $page;
        $arr['last_page'] = ceil($total / $perpage);
        $arr['data'] = $user;

        return response()->json($arr, 201);
    }
}
