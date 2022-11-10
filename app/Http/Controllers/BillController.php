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
            'total_room_rate' => 'required|numeric|min:0',
            'total_service_fee' => 'required|numeric|min:0',
            'total_money' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $bill = Bill::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => "Created bill successfully",
            'data' => $bill
        ];
        return response()->json($arr,201);
    }



    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'total_room_rate' => 'required|numeric|min:0',
            'total_service_fee' => 'required|numeric|min:0',
            'total_money' => 'required|numeric|min:0'
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
            'message' => 'Change infomation for bill successfully update !',
            'service' => $id
        ], 201);
    }



    //Room Info
    public function billInfo($id)
    {
        $bill = Bill::find($id);
        if (empty($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Unknown bill information',
                'data' => []
            ];
            return response()->json($arr, 200);
        }

        $arr = [
            'HTTP Code' => '200',
            'message' => "Detail bill information",
            'data' => $bill
        ];
        return response()->json($arr, 201);
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
        $bill = Bill::where('id', $id)->update(
            ['status' => $request->status]
        );
        return response()->json([
            'HTTP Code' => '200',
            'message' => 'Hiden bill successfully ',
            'service' => $id,
        ], 201);
    }



    //get list by room
    public function getListTotalRoomBy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_room_from' => 'required|numeric|min:0',
            'total_room_to' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $bill = Bill::whereBetween('total_room_rate', [$request->total_room_from, $request->total_room_to])
            ->get();
        // dd($bill[0]);
        if (!empty($bill[0])) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'List bill ',
                'data' => $bill,
            ];
            return response()->json($arr, 201);
        }
        $arr = [
            'HTTP Code' => '200',
            'message' => 'Unknown bill information',
            'data' => []
        ];
        return response()->json($arr, 200);
    }



    //get list by service
    public function getListTotalServiceBy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_service_from' => 'required|numeric|min:0',
            'total_service_to'=> 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $bill = Bill::whereBetween('total_room_rate', [$request->total_service_from, $request->total_service_to])
            ->get();
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Unknown bill information',
                'data' => []
            ];
            return response()->json($arr, 200);
        }

        return response()->json([
            'HTTP Code' => '200',
            'message' => 'List bill ',
            'data' => $bill,
        ], 201);
    }

    

    //get list by total
    public function getListTotalBy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_from' => 'required|numeric|min:0',
            'total_to' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $bill = Bill::whereBetween('total_room_rate', [$request->total_from, $request->total_to])
            ->get();
        if (is_null($bill)) {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Unknown bill information',
                'data' => []
            ];
            return response()->json($arr, 200);
        }

        return response()->json([
            'HTTP Code' => '200',
            'message' => 'List bill ',
            'data' => $bill,
        ], 201);
    }
}
