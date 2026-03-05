<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required'
        ]);

        if(Auth::attempt( $request->only('email', 'password'))){

            return response()->json([
                'token' => $request->user()->createToken($request->name)->plainTextToken ,
                'message' => 'Success',
            ],200);

        }else{

            return response()->json([
                'message' => 'Unauthenticated',
            ],401);

        }
        
    }
    
}
