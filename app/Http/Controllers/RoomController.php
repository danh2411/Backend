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
        // $room = Room::all()->paginate(10);
        //check 3 ngôi 
        
        $room = Room::paginate(1);
        
        $arr = [
            'HTTP Code' => 200,
            'message' => "List Room",
            'data' => $room
        ];
        return response()->json($arr, 200);
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
            'message' => 'The room information was successfully updated',
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
         $room = Room::find($id);
         if(empty($room)){
            return response()->json([
                'HTTP Code' => '200',
                'message' => 'Unknown room',
                'service' => $id,
            ], 201);
         }
         
         $room = Room::where('id', $id)->update(
             ['status' => $request->status]
         );
         
         return response()->json([
             'HTTP Code' => '200',
             'message' => 'Hiden room successfully ',
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
