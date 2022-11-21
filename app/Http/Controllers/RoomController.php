<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Models\Room;
use Validator;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    //  get list
    public function roomAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'numeric',

        ]);
        $total = 1;
        $page = 1;
        $perpage = 1;
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if (!empty($request->id)) {
            $user = Room::where('id', $request->id)->get();

            if (!empty($user->id)) {
                $arr['message'] = 'Find successful room: ' . $request->id;
            } else {
                $arr['message'] = 'No Room:' . $request->id . ' found';
            }
        } else {


            $query = Room::query();
            $perpage = $request->input('perpage', 9);
            $page = $request->input('page', 1);
            $total = $query->count();
            $user = $query->offset(($page - 1) * $perpage)->limit($perpage)->get();


            $arr['message'] = 'All Rooms';

        }
        $arr['HTTP Code'] = '200';

        $arr['total'] = $total;
        $arr['page'] = $page;
        $arr['last_page'] = ceil($total / $perpage);
        $arr['data'] = $user;

        return response()->json($arr, 201);

    }

    //create
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name_room' => 'required|string|unique',
            'typ_room' => 'required',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $room = Room::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => " Created room successfully",
            'data' => $room
        ];
        return response()->json($arr, 201);
    }


    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name_room' => 'required|string|unique',
            'typ_room' => 'required',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $room = Room::query()->find($id);
        if (!empty($room)) {
            $room = Room::where('id', $id)->update(
                [
                    'name_room' => $request->name_room,
                    'typ_room' => $request->typ_room,
                    'price' => $request->price,
                    'capacity' => $request->capacity
                ]
            );
            return response()->json([
                'HTTP Code' => 200,
                'message' => 'The room information was successfully updated',
                'service' => $id
            ], 201);
        } else {
            return response()->json([
                'HTTP Code' => 200,
                'message' => 'Room not found',
                'service' => null
            ], 201);
        }

    }


    //hiden
    public function hiden(Request $request, $id)
    {
        $user = Room::query()->where('id', $id)->first();

        if (!empty($user)) {
            $status = $user->status === 1 ? 0 : 1;
            Room::where('id', $id)->update(
                ['status' => $status]
            );
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Room status change successful ',
                'client' => $id,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'Room' => $id,
            ];
        }


        return response()->json($arr, 201);
    }

    public function filterStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_bill' => 'numeric|between:0,3',
            'status_room' => 'numeric|between:0,3',
            'from' => 'string',
            'to' => 'string',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
     if (isset($request->from)){
         $bill=Bill::query()->whereBetween('day_in', [$request->from,$request->to])->where('status',$request->status_bill)->get();
foreach ($bill as $b)
{
$da=$b->room_id;

} $rooms = Room::query()->where('id',$da)->get();
dd($rooms);
     }else{
        $rooms = Room::query()->where('status', $request->status)->get();
    }




    }

}
