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
use function Symfony\Component\String\b;


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
            'client_id' => 'required|numeric|min:0',
            'day_in' => 'required|numeric|min:0',
            'day_out' => 'required|numeric|min:0',
            'room_id' => 'required|numeric|min:0',
            'service_id' => 'min:0',
            'amount' => 'min:0',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $books = null;
        $services = $request->input('service_id');
        $sl = $request->input('amount');
        foreach ($services as $key => $service) {
            $books[] = Services::query()->where('id', $service)->first();
        }
        foreach ($books as $book) {
            $dat[] = $book->price;
        }
        $price_service = array_sum($dat) * array_sum($sl);
        $room = Room::query()->find($request->room_id)->first();
        $price = ($request->day_out - $request->day_in) / 86400 * $room->price;
        $total = $price_service + $price;
        $client = Client::find($request->client_id);

        if (!empty($room->id) && !empty($client->id)) {

            $bill = Bill::create(array_merge(
                $validator->validated(),
                ['total_money' => $total],
                ['total_service_fee' => (int)$price_service],
                ['total_room_rate' => (int)$price],
                ['account_id' => $user->id]
            ));
            $data[] = 0;
            if (!empty($request->service_id)) {
                $price_service = array_sum($dat) * array_sum($sl);
                $data['ser'] = array_combine($services, $sl);
                $data['client_id'] = $request->client_id;
                $data['bill_id'] = $bill->id;
                foreach ($data['ser'] as $key => $book) {
                    Booked::query()->create(array_merge(
                        ['client_id' => $data['client_id'],
                            'services_id' => $key,
                            'amount' => $book,
                            'bill_id' => $data['bill_id']],
                    ));
                }
            }
            $arr = [
                'HTTP Code' => 200,
                'message' => "Created bill successfully",
                'data' => $bill
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

        $user = Auth::user();

        $input = $request->all();
        $validator = Validator::make($input, [

            'client_id' => 'required|numeric|min:0',
            'day_in' => 'required|numeric|min:0',
            'day_out' => 'required|numeric|min:0',
            'room_id' => 'required|numeric|min:0',
            'service_id' => 'min:0',
            'amount' => 'min:0',

        ]);
//        if ($validator->fails()) {
//            return response()->json($validator->errors()->toJson(), 400);
//        }
        $amounts = $request->amount;
        $services = [];
        $books = null;
        $services = $request->input('service_id');

        foreach ($services as $key => $service) {
            $books[] = Services::query()->where('id', $service)->first();
        }
        foreach ($books as $book) {
            $dat[] = $book->price;
        }
        $sl = $request->input('amount');
        $price_service = array_sum($dat) * array_sum($sl);
        $room = Room::query()->find($request->room_id)->first();
        $price = ($request->day_out - $request->day_in) / 86400 * $room->price;
        $total = $price_service + $price;
        $client = Client::find($request->client_id);
        $bill = Bill::query()->find($id);

        $data['ser'] = array_combine($services, $sl);

        if (!empty($bill->id) && !empty($room->id) && !empty($client->id)) {
            $bill = Bill::query()->where('id', $id)->update(
                ['account_id' => $user->id,
                    'room_id' => $room->id,
                    'client_id' => $client->id,
                    'day_in' => $request->day_in,
                    'day_out' => $request->day_out,
                    'total_money' => $total,
                    'total_service_fee' => (int)$price_service,
                    'total_room_rate' => (int)$price,

                ],

            );
            if (!empty($request->service_id)) {
                $bos = Booked::query()->where('bill_id', $id)->get();

                foreach ($bos as $bo){
                  foreach ($data['ser'] as $key=>$da){dd($key[0]);
                      Booked::query()->where('id', $bo->id)->update(
                          [
                              'services_id' => $key,
                              'amount' => $da,
                             ]
                      );
                  }
                }

            }
            $arr = [
                'HTTP Code' => 200,
                'message' => "Update bill successfully",
                'data' => $user
            ];
        } else {
            $arr = [
                'HTTP Code' => 200,
                'message' => "Bill  not found ",
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


    //hiden status room
    public function hiden(Request $request, $id)
    {
        $bill = Bill::query()->where('id', $id)->first();

        if (!empty($bill)) {
            $status = $bill->status === 1 ? 0 : 1;
            $user = Bill::where('id', $id)->update(
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

//status clear
    public function Pay(Request $request, $id)
    {
        $bill = Bill::query()->where('id', $id)->first();

        if (!empty($bill)) {
            $status = $bill->status === 1 ? 2 : 3;
            $user = Bill::where('id', $id)->update(
                ['status' => $status]
            );
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill status change successful ',
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
