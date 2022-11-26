<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = Account::query()->where('email', $request->email)->first();


        if ($user->status == 0 || !$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:accounts',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Account::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        if (!empty(auth()->user()->id)) {
            return response()->json(auth()->user());
        }

    }

    public function allAccount(Request $request)
    {

        $query = Account::query();
        $perpage = $request->input('perpage', 9);
        $page = $request->input('page', 1);
        $total = $query->count();
        $user = $query->offset(($page - 1) * $perpage)->limit($perpage)->get();


        $arr['message'] = 'All clients';

        $arr['HTTP Code'] = '200';

        $arr['total'] = $total;
        $arr['page'] = $page;
        $arr['last_page'] = ceil($total / $perpage);
        $arr['data'] = $user;

        return response()->json($arr, 201);
    }

    public function oneAccount($id)
    {
        $user = Account::query()->find($id);

        if (!empty($user->id)) {
            $arr['HTTP Code'] = '200';
            $arr['message'] = 'Info account id:' . $id;
            $arr['data'] = $user;
        } else {
            $arr['HTTP Code'] = '200';
            $arr['message'] = 'Account not found';
            $arr['data'] = $user;
        }
        return response()->json(
            $arr, 201);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
//            'user' => auth()->user()
        ]);
    }

    public function editAccount(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'phone' => 'required|string|min:10|max:11',
            'address' => 'required|string',
            'CCCD' => 'required|string|max:13',
            'role' => 'required|numeric',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = Account::query()->find($id);


        if (!empty($user)) {
            $id = $user->id;
            if (!empty($request->role)) {
                $role = Group::query()->find($request->role);

                if (!empty($role)) {
                    Account::query()->where('id', $id)->update(['group_id' => $request->role,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'CCCD' => $request->CCCD,
                    ]);
                    $data['HTTP Code'] = 200;
                    $data['Account'] = $id;
                    $data['message'] = 'The account was successfully updated';
                } else {
                    $data['HTTP Code'] = 200;
                    $data['Account'] = null;
                    $data['message'] = 'The account update failed.Role not found';
                }

            } else {
                Account::query()->where('id', $id)->update(['group_id' => $request->role,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'CCCD' => $request->CCCD,
                ]);
                $data['HTTP Code'] = 200;
                $data['Account'] = $id;
                $data['message'] = 'The account was successfully updated';
            }
        } else {
            $data['HTTP Code'] = 200;
            $data['Account'] = null;
            $data['message'] = 'Account not found';
        }
        return response()->json($data, 201);
    }

    public function updateProflie(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'phone' => 'required|string|min:10|max:11',
            'address' => 'required|string',
            'CCCD' => 'required|string|max:13',
            'role' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $id = Auth::user()->id;

        if (!empty($request->role)) {
            $role = Group::query()->find($request->role);

            if (!empty($role)) {
                Account::query()->where('id', $id)->update(['group_id' => $request->role,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'CCCD' => $request->CCCD,
                ]);
                $data['HTTP Code'] = 200;
                $data['Account'] = $id;
                $data['message'] = 'The account was successfully updated';
            } else {
                $data['HTTP Code'] = 200;
                $data['Account'] = null;
                $data['message'] = 'The account update failed.Role not found';
            }

        } else {
            Account::query()->where('id', $id)->update(['group_id' => $request->role,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'CCCD' => $request->CCCD,
            ]);
            $data['HTTP Code'] = 200;
            $data['Account'] = $id;
            $data['message'] = 'The account was successfully updated';
        }
        return response()->json($data, 201);
    }

    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = Account::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );
        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $userId,
        ], 201);
    }

    public function hiden(Request $request, $id)
    {
        $user = Account::query()->where('id', $id)->first();

        if (!empty($user)) {
            $status = $user->status === 1 ? 0 : 1;

            $user = Account::where('id', $id)->update(
                ['status' => $status]
            );
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Account status change successful ',
                'account' => $id,
            ];
        } else {
            $arr = [
                'HTTP Code' => '200',
                'message' => 'Not found ',
                'account' => $id,
            ];
        }
        return response()->json($arr, 201);
    }
}
