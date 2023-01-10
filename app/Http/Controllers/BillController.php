<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Booked;
use App\Models\Client;
use App\Models\Room;
use App\Models\Services;
use Carbon\Carbon;
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


    ///
    public function create(Request $request)
    {
        $user = Auth::user();

        $input = $request->all();
        $validator = Validator::make($input, [
            'client_id' => 'required|numeric|min:0',
            'day_in' => 'required|string',
            'day_out' => 'required|string',
            'room_id' => 'required|numeric|min:0',
            'service_id' => 'min:0',
            'amount' => 'min:0',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $da = date("Y-m-d H:i:s", now()->timestamp);
        $date = Carbon::parse($da)->timestamp;
        $day_in = Carbon::parse($request->day_in)->timestamp;
        $day_out = Carbon::parse($request->day_out)->timestamp;

        if ($day_in + 46800 < $date) {
            $mess = ['day_in' => "Please select a future date"];
            return response()->json(json_encode($mess), 400);
        }
        if ($day_out + 46800 < $date || $day_out <= $day_in) {
            $mess = ['day_out' => "Please select a future date"];
            return response()->json(json_encode($mess), 400);
        }

        $books = null;
        $services = $request->service_id;
        $sl = $request->amount;

        $books = Services::query()->where('id', $services)->first();

        $dat[] = 0;
        if (!empty($books)) {
            $dat[] = $books->price;

            // foreach ($books as $book) {
            //     $dat[] = $book->price;
            // }
        }


        $price_service = array_sum($dat) * $sl;
        $room = Room::query()->find($request->room_id);

        //timestamp
        $day_in = Carbon::parse($request->day_in)->timestamp;
        $day_out = Carbon::parse($request->day_out)->timestamp;
        $price = ($day_out - $day_in) / 86400 * $room->price;

        $total = $price_service + $price;
        $client = Client::find($request->client_id);

        if (!empty($room->id) && !empty($client->id)) {

            $bill = Bill::create(array_merge(
                $validator->validated(),
                ['day_in' => $day_in],
                ['day_out' => $day_out],
                ['total_money' => $total],
                ['total_service_fee' => (int)$price_service],
                ['total_room_rate' => (int)$price],
                ['account_id' => $user->id]
            ));
            $data[] = 0;
            if (!empty($request->service_id)) {

                $data['client_id'] = $request->client_id;
                $data['bill_id'] = $bill->id;
                Booked::query()->create(array_merge(
                    ['client_id' => $data['client_id'],
                        'services_id' => $services,
                        'amount' => $sl,
                        'bill_id' => $data['bill_id']],
                ));
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

///
    //edit
    public function edit(Request $request, $id)
    {

        $user = Auth::user();

        $input = $request->all();
        $validator = Validator::make($input, [

            'client_id' => 'required|numeric|min:0',
            'day_in' => 'required|string',
            'day_out' => 'required|string',
            'room_id' => 'required|numeric|min:0',
            'service_id' => 'min:0',
            'amount' => 'min:0',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $amounts = $request->amount;
        $services = [];
        $books = null;
        $services = $request->input('service_id');

        foreach ($services as $key => $service) {
            $books[] = Services::query()->where('id', $service)->get();
        }

        foreach ($books as $book) {
            $dat[] = $book->price;
        }
        $sl = $request->input('amount');
        $price_service = array_sum($dat) * array_sum($sl);
        $day_out = Carbon::parse($request->day_out)->timestamp;
        $day_in = Carbon::parse($request->day_in)->timestamp;
        $room = Room::query()->find($request->room_id)->first();
        $price = ($day_out - $day_in) / 86400 * $room->price;
        $total = $price_service + $price;
        $client = Client::find($request->client_id);
        $bill = Bill::query()->find($id);

        if (!empty($bill->id) && !empty($room->id) && !empty($client->id)) {
            Bill::query()->where('id', $id)->update(
                ['account_id' => $user->id,
                    'room_id' => $room->id,
                    'client_id' => $client->id,
                    'day_in' => $day_in,
                    'day_out' => $day_out,
                    'total_money' => $total,
                    'total_service_fee' => (int)$price_service,
                    'total_room_rate' => (int)$price,

                ],

            );
            if (!empty($request->service_id)) {
                $bos = Booked::query()->where('bill_id', $id)->delete();
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
            Room::query()->where('id', $room->id)->update(['status' => 4]);
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

        if (!empty($bill) && $bill->status = 1) {
            if ($bill->status = 1 || $bill->status = 0) {
                $status = $bill->status == 1 ? 0 : 1;
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
                    'message' => 'Bill not change ',
                    'client' => $id,
                ];
            }

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
            $status = $bill->status == 1 ? 2 : 3;
            $user = Bill::where('id', $id)->update(
                ['status' => $status]
            );
            Room::query()->where('id', $bill->room_id)->update(['status' => 3]);
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

    public function checkin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $room = Room::query()->find($request->room_id);
        // dd($room);
        if ($room->id) {
            if ($room->status == 1) {
                $name = Room::query()->where('id', $room->id)->update(['status' => 2]);


                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Change successful',
                    'data' => [],
                ];
            } else {
                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Room is not set before',
                    'data' => [],
                ];
            }
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Room not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);
    }

    public function billroom($id)
    {

        $bill = Bill::query()->where('room_id', $id)->where('status', 1)->first();

        if (!empty($bill)) {
            $ser = Booked::query()->where('bill_id', $bill->id)->get();
            $rooom = Room::query()->where('id', $bill->room_id)->get();
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill info',
                'data' => $bill,
                'services' => $ser,
                'room' => $rooom
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);


    }

    public function roomprice($id)
    {
        $room = Room::query()->where('id', $id)->first();

        $price = Room::query()->where('price', $room->price)->get();
        $arr = [
            'HTTP Code' => '200',
            'message' => 'Bill not found',
            'data' => $price,
        ];

        return response()->json($arr, 200);
    }

    public
    function changroom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill' => 'required|numeric|min:0',
            'room' => 'required|numeric|min:0',
            'total_room_rate' => 'required|numeric|min:0',
            'total_money' => 'required|numeric|min:0',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $bill = Bill::query()->where('id', $request->bill)->where('status', 1)->first();
        $room = Room::query()->where('id', $bill->room_id)->first();

        if ($room->status == 2) {
            $check = Room::query()->where('id', $request->room)->first();
            if ($room->price == $check->price) {

                Bill::query()->where('id', $request->bill)->update(['room_id' => $request->room,
                    'total_room_rate' => $request->total_room_rate,
                    'total_money' => $request->total_money
                ]);
                Room::query()->where('id', $bill->room_id)->update(['status' => 3]);
                Room::query()->where('id', $request->room)->update(['status' => 2]);
                $bill = Bill::query()->where('room_id', $request->room)->where('status', 1)->first();

                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Done',
                    'data' => $bill,
                ];
            } else {
                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Not the same price',
                    'data' => [],
                ];
            }


        }
        if ($room->status == 1) {
            Bill::query()->where('id', $request->bill)->update(['room_id' => $request->room]);
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Done',
                'data' => $bill,
            ];
        }
        return response()->json($arr, 200);

    }

    public
    function billservice($id)
    {

        $bill = Bill::query()->where('room_id', $id)->where('status', 1)->first();


        if (!empty($bill)) {
            $service = Booked::query()->where('bill_id', $bill->id)->get();
            $list = [];

            $ad = [];
            if (!empty($service)) {
                foreach ($service as $key => $item) {

                    $list[$key]['amount'] = $item['amount'];
                    $list[$key]['id'] = $item['id'];
                }
                foreach ($service as $key => $se) {
                    $ad = Services::query()->where('id', $se['services_id'])->first();
                    $list[$key]['price'] = $ad['price'] * $list[$key]['amount'];
                    $list[$key]['name'] = $ad['name'];
                }

            }

            Room::query()->where('id', $bill->room_id)->first();
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill info',
                'bill' => $bill,
                'service' => $list,

            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);


    }

    public
    function addservice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill' => 'required|numeric|min:0',
            'service' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:1',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $bill = Bill::query()->where([['id', $request->bill], ['status', 1]])->first();
        if (!empty($bill)) {
            $ser = Services::query()->where('id', $request->service)->first();
            if (!empty($ser->id)) {
                Booked::query()->create(array_merge(
                    ['client_id' => $bill->client_id,
                        'services_id' => $ser->id,
                        'amount' => $request->amount,
                        'bill_id' => $bill->id],
                ));
                $price = $ser->price * $request->amount + $bill->total_service_fee;

                $total = $price + $bill->total_money - $bill->total_service_fee;

                Bill::query()->where('id', $request->bill)->update(['total_service_fee' => $price, 'total_money' => $total]);
                $bill = Bill::query()->where([['id', $request->bill], ['status', 1]])->first();

                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Service ',
                    'bill' => $bill,
                ];
            } else {
                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Service not found',
                    'data' => [],
                ];
            }

        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);

    }

    public
    function deletesevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill' => 'required|numeric|min:0',
            'service' => 'required|numeric|min:0',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $bill = Bill::query()->where([['id', $request->bill], ['status', 1]])->first();
        $check = Booked::query()->where([['bill_id', $request->bill], ['id', $request->service]])->first();

        if (!empty($bill) && !empty($check)) {


            $as = Services::query()->where('id', $check->services_id)->first();

            $total = $bill->total_money - $as->price*$check->amount;
            $se = $bill->total_service_fee - $as->price*$check->amount;

            Bill::query()->where([['id', $request->bill], ['status', 1]])->update([
                'total_service_fee' => $se,
                'total_money' => $total,
            ]);
            Booked::query()->where([['bill_id', $request->bill], ['id', $request->service]])->delete();

            $arr = [
                'HTTP Code' => '200',
                'message' => 'Done',

            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',

            ];
        }
        return response()->json($arr, 200);

    }

    public
    function clientroom($id)
    {
        $bill = Bill::query()->where([['room_id', $id], ['status', 1]])->first();

        if (!empty($bill)) {
            $client = Client::query()->where('id', $bill->client_id)->first();
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Client',
                'data' => $client,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);

    }

    public function getByMoth(Request $request)
    {
        $bill = Bill::query()->where('status', 2)->get();
        if (!empty($bill)) {
            if ($request->by == 'm') {
                $da = date("Y-m-d", now()->timestamp);
                $date = Carbon::parse($da)->timestamp;
                $da = [];
                foreach ($bill as $as) {
                    $to = $as->day_out;

                    if ($date - $to < 2592000) {
                        $da[] = $as->total_money;

                    }

                }
                $total = array_sum($da);

                $arr = [
                    'HTTP Code' => '200',
                    'message' => 'Total money 30day',
                    'data' => $total,
                ];
            }
            if ($request->by == 'd') {
                if (!empty($bill)) {
                    $da = date("Y-m-d", now()->timestamp);
                    $date = Carbon::parse($da)->timestamp;
                    $da = [];
                    foreach ($bill as $as) {
                        $to = $as->day_out;

                        if ($date - $to < 604800) {
                            $da[] = $as->total_money;

                        }

                    }
                    $total = array_sum($da);

                    $arr = [
                        'HTTP Code' => '200',
                        'message' => 'Total money 7day',
                        'data' => $total,
                    ];
                }
            }

            if ($request->by == 'h') {

                if (!empty($bill)) {
                    $da = date("Y-m-d", now()->timestamp);
                    $date = Carbon::parse($da)->timestamp;
                    $da = [];
                    foreach ($bill as $as) {
                        $to = $as->day_out;

                        if ($date - $to < 86400) {
                            $da[] = $as->total_money;

                        }

                    }
                    $total = array_sum($da);

                    $arr = [
                        'HTTP Code' => '200',
                        'message' => 'Total money 1day',
                        'data' => $total,
                    ];
                }

            }
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not money',
                'data' => [],
            ];
        }

        return response()->json($arr, 201);
    }

    public function listBill(Request $request)
    {

        $bill = Bill::query()->get();
        if (!empty($bill)) {

            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill',
                'data' => $bill,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);
    }

    public function viewBill(Request $request)
    {

        $bill = Bill::query()->find($request->id);
        if (!empty($bill)) {

            $room = Room::query()->find($bill->room_id);
            $client = Client::query()->find($bill->client_id);
            $ab = Account::query()->find($bill->account_id);
            $ser = Booked::query()->where('bill_id', $bill->id)->get();
            $list = [];
            if (!empty($ser)) {
                foreach ($ser as $key => $item) {
                    $list[$key]['id'] = $item['id'];
                    $list[$key]['amount'] = $item['amount'];

                }
                foreach ($ser as $key => $se) {
                    $ad = Services::query()->where('id', $se['services_id'])->first();
                    $list[$key]['price'] = $ad['price'] * $list[$key]['amount'];
                    $list[$key]['name'] = $ad['name'];
                }
            }


            $bi['id'] = $bill->id;
            $bi['name_room'] = $room->name_room;
            $bi['name'] = $ab->name;
            $bi['email'] = $ab->email;
            $bi['firtname'] = $client->firtname;
            $bi['lastname'] = $client->lastname;
            $bi['phone'] = $client->phone;
            $bi['CCCD'] = $client->CCCD;
            $bi['price_room'] = $room->price;
            $bi['amount'] = 1;
            $bi['service'] = $list;
            $bi['total_room_rate'] = $bill->total_room_rate;
            $bi['total_service_fee'] = $bill->total_service_fee;
            $bi['total_money'] = $bill->total_money;

            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill',
                'data' => $bi,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Bill not found',
                'data' => [],
            ];
        }
        return response()->json($arr, 200);
    }

    public function staticRoom()
    {

        $bill = Bill::query()->where('status', 2)->get();
        if (!empty($bill)) {
            $da = date("Y-m-d", now()->timestamp);
            $date = Carbon::parse($da)->timestamp;
            $da = [];

            foreach ($bill as $key => $as) {
                $to = $as->day_out;
                $to = $as->day_out;

                if ($date - $to < 2592000) {
                    $room=Room::query()->find($as->room_id);

                $da[$room->name_room][$key] = $as->total_room_rate;

                $total[$room->name_room]['id']= $as->room_id;
                $total[$room->name_room]['total'] = array_sum(   $da[$room->name_room]);

                }
            }

                foreach ($total as $key=>$value){

                $room=Room::query()->find($value['id']);

                    $ro[$key]= $total[$room->name_room]['total'];

            }

            $arr = [
                'HTTP Code' => '200',
                'message' => 'Total money 30day',
                'data' => $ro,
            ];
            return response()->json($arr, 200);
        }
    }
}
