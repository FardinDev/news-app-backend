<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request){

        $validator = Validator::make(
            $request->all(),
            [

                'name' => 'required|string|min:2|max:25',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',

            ],
           
        );

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), null, 400);
        }

        try {

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
    
            $response = $this->formatUserResponse($user);

            DB::commit();

            return $this->apiSuccessResponse('Successfully registered', $response);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return $this->apiFailedResponse('Something went wrong', null, 500);
        }

    }

    public function login(Request $request){

        $validator = Validator::make(
            $request->all(),
            [

                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6',

            ],
           
        );

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), null, 400);
        }

        try {

            DB::beginTransaction();

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->apiFailedResponse('Invalid credentials', null, 401);
            }
    
            $user = Auth::user();
    
            $response = $this->formatUserResponse($user);

            DB::commit();

            return $this->apiSuccessResponse('Successfully logged in', $response);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return $this->apiFailedResponse('Something went wrong', null, 500);
        }

    }

    private function formatUserResponse(User $user)
    {

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ];
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiSuccessResponse('Successfully logged out',);
    }

}
