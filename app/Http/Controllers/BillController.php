<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Booked;
use App\Models\Client;
use App\Models\Room;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use Validator;


class BillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    //create
    public function create(Request $request)
    {
        $user = Auth::user();

        $input = $request->all();
        $validator = Validator::make($input, [

            'status' => 'required|numeric|min:0',
            'received_date' => 'required|numeric|min:0',
            'client_id' => 'required|numeric|min:0',
            'day_in' => 'required|numeric|min:0',
            'day_out' => 'required|numeric|min:0',
            'room_id' => 'required|numeric|min:0',
            'service_id' => 'numeric|min:0',
            'amount' => 'numeric|min:0',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);

        }
        $price_service = 0;
        if (!empty($request->service_id)) {
            $book = Services::query()->find($request->service_id)->first();
            $price_service = $book->price * $request->amount;

        }
        $room = Room::query()->find($request->room_id)->first();
        $price = ($request->day_out - $request->day_in) / 86400 * $room->price;
        $total = $price_service + $price;
        $client = Client::find($request->client_id);

        if (!empty($room->id) && !empty($client->id)) {

            $user = Bill::create(array_merge(
                $validator->validated(),
                ['total_money' => $total],
                ['total_service_fee' => (int)$price_service],
                ['total_room_rate' => (int)$price],
                ['account_id' => $user->id]
            ));

            if (!empty($request->service_id)) {

                Booked::query()->create(array_merge(
                    ['client_id'=>$request->client_id],
                    ['services_id'=>$request->service_id],
                    ['amount'=>$request->amount],
                    ['bill_id'=>$user->id]
                ));
            }

            $arr = [
                'HTTP Code' => 200,
                'message' => "Created bill successfully",
                'data' => $user
            ];
        } else {
            $arr = [
                'HTTP Code' => 200,
                'message' => "Client or Room not found",

            ];
        }

        return response()->json($arr, 201);
    }


    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'payday' => 'required|numeric|min:0',
            'status' => 'required|numeric|min:0',
            'received_date' => 'required|numeric|min:0',
            'client_id' => 'required|numeric|min:0',
            'room_id' => 'required|numeric|min:0',
            'total_room_rate' => 'required|numeric|min:0',
            'total_service_fee' => 'required|numeric|min:0',
            'total_money' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $room = Room::find($request->room_id);
        $client = Client::find($request->client_id);
        if (!empty($room->id) && !empty($client->id)) {

            $bill = Bill::where('id', $id)->update(
                ['payday' => $request->payday,
                    'status' => $request->status,
                    'received_date' => $request->received_date,
                    'client_id' => $request->client_id,
                    'room_id' => $request->room_id,
                    'total_room_rate' => $request->total_room_rate,
                    'total_service_fee' => $request->total_service_fee,
                    'total_money' => $request->total_money
                ]
            );

            $arr = [
                'HTTP Code' => 200,
                'message' => "Update bill successfully",
                'data' => $bill
            ];
        } else {
            $arr = [
                'HTTP Code' => 200,
                'message' => "Update or Room not found",

            ];
        }

        return response()->json($arr, 201);
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
            'total_service_to' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $bill = Bill::whereBetween('total_room_rate', [$request->total_service_from, $request->total_service_to])
            ->get();
        if (!empty($bill[0])) {
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
        if (!empty($bill[0])) {
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
