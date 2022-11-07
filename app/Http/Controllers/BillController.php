<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Validator;


class BillController extends Controller
{
   
    
    //create 
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [

            'total_room_rate' => 'required',
            'total_service_fee' => 'required',
            'total_money' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $bill = Bill::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => "Hóa đơn đã tạo thành công",
            'data' => $bill
        ];
        return response()->json($arr, 201);
    }



      //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'total_room_rate' => 'required',
            'total_service_fee' => 'required',
            'total_money' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $bill = Bill::where('id', $id)->update(
            [
                'total_room_rate' => $request->total_room_rate,
                'total_service_fee' =>  $request->total_service_fee,
                'total_money' =>  $request->total_money 
            ]
        );

        return response()->json([
            'HTTP Code' => 200,
            'message' => 'Thay đổi thông tin hóa đơn thành công ! ',
            'service' => $id
        ], 201);
    }
   

  
    //Room Info
     public function billInfo($id)
     {
         $bill = Bill::find($id);
         if (is_null($bill)) {
             $arr = [
                 'HTTP Code' => '200',
                 'message' => 'Không có thông tin hóa đơn này',
                 'data' => []
             ];
             return response()->json($arr, 200);
         }

         $arr = [
             'HTTP Code' => '200',
             'message' => "Chi tiết thông tin hóa đơn",
             'data' => $bill
         ];
         return response()->json($arr, 201);
     }


     //cancel 
     public function delete($id){
        $bill = Bill::find($id);
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Hóa đơn không tồn tại',
                'data' => []
            ];
            return response()->json($arr, 200);
        }
        Bill::destroy($id);
        $arr = [
            'HTTP Code' => '200',
           'message' =>'Hóa đơn đã được hủy bỏ',
           'data' => []
        ];
        return response()->json($arr, 200);
     }


}
