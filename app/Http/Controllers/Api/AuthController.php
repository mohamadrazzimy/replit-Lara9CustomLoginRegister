<?php
/* app\Http\Controllers\Api\AuthController.php */

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $created_user= User::where('email', '=', $request->email)->first();

        return response()->json([
            'user'=>$created_user,
            'stus'=>'registered'
            ], 200);  
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'user' => Null,
                    'message' => 'Invalid login details',
                    'stus' => 'failed',
                ],
                200
            );
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $user_loggedin=[
            'id' => $user->id,
            'email' => $user->email,
            'stus'=>'loggedin'
        ];


        $token = $user->createToken('auth_token')->plainTextToken;
        $user_loggedin['user_token']= $token;
        $user_loggedin['token_type']= 'Bearer';


        return response()->json(
            $user_loggedin,
            200
        );

    }
}
