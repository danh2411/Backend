<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;
use Validator;

class ServiceController extends Controller
{
    //get all
    // public function serviceAll()
    // {
    //     $service = Services::all();
    //     $arr = [
    //         'HTTP Code' => 200,
    //         'message' => "Danh sách dịch vụ",
    //         'data' => $service
    //     ];
    //     return response()->json($arr, 200);
    // }
    

    //create
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $service = Services::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => "Sản phẩm đã lưu thành công",
            'data' => $service
        ];
        return response()->json($arr, 201);
    }



    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $service = Services::where('id', $id)->update(
            [
                'name' => $request->name,
                'price' => $request->price,
            ]
        );

        return response()->json([
            'HTTP Code' => 200,
            'message' => 'Thay đổi thông tin dịch vụ thành công ! ',
            'service' => $id
        ], 201);
    }



    //hiden
    public function hiden(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $service = Services::where('id', $id)->update(
            ['status' => $request->status]
        );
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Hiden service successfully ',
            'service' => $id,
        ], 201);
    }


 

    //serviceInfo
    public function serviceInfo($id)
    {
        $service = Services::find($id);
        if (is_null($service)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Không có dịch vụ này',
                'data' => []
            ];
            return response()->json($arr, 200);
        }
        $arr = [
            'HTTP Code' => '200',
            'message' => "Chi tiết dịch vụ ",
            'data' => $service
        ];
        return response()->json($arr, 201);
    }
}
