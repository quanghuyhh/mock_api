<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Requests\Api\Auth\SignUpRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use App\Http\Resources\Api\V1\UserResource;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(SignUpRequest $request)
    {
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginRequest $request)
    {
        DB::beginTransaction();
        try {
            $credentials = request(['email', 'password']);

            if(!Auth::attempt($credentials))
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);

            Passport::personalAccessTokensExpireIn(now()->addYears(100));
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            
            DB::commit();
            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->format(FORMAT_OUTPUT)
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'fail to login'], 401);
        }
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->user()->token()->revoke();

            DB::commit();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Fail to log out'
            ], 400);
        }
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->key = $user->getKey();
        UserResource::withoutWrapping();
        return UserResource::make($request->user());
    }
}
