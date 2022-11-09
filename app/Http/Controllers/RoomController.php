<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Validator;

class RoomController extends Controller
{

    //  get list
    public function roomAll()
    {
        $room = Room::all();
        $arr = [
            'HTTP Code' => 200,
            'message' => "Danh sách phòng",
            'data' => $room
        ];
        return response()->json($arr, 200);
    }


    //create
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name_room' => 'required|string',
            'typ_room' => 'required',
            'price' => 'required|integer',
            'capacity' => 'required|integer',

            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $room = Room::create($input);
        $arr = [
            'HTTP Code' => 200,
            'message' => "Thông tin phòng đã lưu thành công",
            'data' => $room
        ];
        return response()->json($arr, 201);
    }
   


    //edit
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name_room' => 'required|string',
            'typ_room' => 'required',
            'price' => 'required|integer',
            'capacity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $room = Room::where('id', $id)->update(
            [
                'name_room' => $request->name_room,
                'typ_room' =>  $request->typ_room,
                'price' =>  $request->price,
                'capacity' =>  $request->capacity
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
         $room = Room::where('id', $id)->update(
             ['status' => $request->status]
         );
         return response()->json([
             'HTTP Code' => '200',
             'message' => 'Hiden service successfully ',
             'service' => $id,
         ], 201);
     }

     

     //Room Info
    //  public function roomInfo($id)
    //  {
    //      $room = Room::find($id);
    //      if (is_null($room)) {
    //          $arr = [
    //              'HTTP Code' => '200',
    //              'message' => 'Không có thông tin phòng này',
    //              'data' => []
    //          ];
    //          return response()->json($arr, 200);
    //      }

    //      $arr = [
    //          'HTTP Code' => '200',
    //          'message' => "Chi tiết thông tin phòng",
    //          'data' => $room
    //      ];
    //      return response()->json($arr, 201);
    //  }
}
