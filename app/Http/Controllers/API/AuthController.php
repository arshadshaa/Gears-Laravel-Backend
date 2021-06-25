<?php

namespace App\Http\Controllers\API;

use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function getAuthors(Request $request){
        $currentuser = Auth::user();
        $authors = User::where("id", "!=", $currentuser->id)->get();
        return response([ 'authors' => $authors, 'message' => 'Retrieved successfully'], 200);
    }
    
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        if (auth()->user()->user_status == 0) {
            return response(['message' => 'User is innactive']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);

    }

    public function logout(Request $request)
        { 
            $user = Auth::user()->token();
            $user->revoke();
            return 'logged out';
        }


     public function changeUserStatus(Request $request)
    {
        $currentuser = Auth::user();
        if ($currentuser->role_id != 1) {
              return response(['error' => "You are not an admin", 'Validation Error']);
        }

        $userID = $request->id;
        $user = User::find($userID);
        if ($user) {
            if ($user->user_status == 1) {
                $user->user_status = 0;
            } else {
                 $user->user_status = 1;
            }
            $user->save();
        } else {
            return response(['error' => "User not found", 'Validation Error']);
        }

        return response([ 'users' => $user, 'message' => 'Retrieved successfully'], 200);

    }
}