<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
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

}
