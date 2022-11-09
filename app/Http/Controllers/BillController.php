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

            'total_room_rate' => 'required|integer',
            'total_service_fee' => 'required|integer',
            'total_money' => 'required|integer'

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
            'total_room_rate' => 'required|integer',
            'total_service_fee' => 'required|integer',
            'total_money' => 'required|integer'
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

     public function getListTotalRoomBy(Request $request) {
        $bill = Bill::whereBetween('total_room_rate', [$request->total_room_from, $request->total_room_to])
        ->get();
        // dd($bill);
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Hóa đơn không tồn tại',
                'data' => []
            ];
            return response()->json($arr, 200);
        }
        $arr=[
            'HTTP Code' => '200',
            'message' => 'Danh sách hóa đơn ',
            'data' => $bill,
        ];
        return response()->json($arr, 201);
    }

    public function getListTotalServiceBy(Request $request) {
        dd($request->total_service_from);
        $bill = Bill::whereBetween('total_room_rate', [$request->total_service_from, $request->total_service_to])
        ->get();
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Hóa đơn không tồn tại',
                'data' => []
            ];
            return response()->json($arr, 200);
        }

        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Danh sách hóa đơn',
            'data' => $bill,
        ], 201);
    }

    public function getListTotalBy(Request $request) {
        $bill = Bill::whereBetween('total_room_rate', [$request->total_from, $request->total_to])
        ->get();
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Hóa đơn không tồn tại',
                'data' => []
            ];
            return response()->json($arr, 200);
        }

        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Danh sách hóa đơn ',
            'data' => $bill,
        ], 201);
    }



}
