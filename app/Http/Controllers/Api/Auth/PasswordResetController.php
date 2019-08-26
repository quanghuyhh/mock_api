<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Notifications\Auth\PasswordResetRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;
use App\Http\Requests\Api\Auth\PasswordCreateRequest;
use App\Http\Requests\Api\Auth\PasswordResetRequest as ApiPasswordResetRequest;
use App\Notifications\Auth\PasswordResetSuccess;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(PasswordCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'We can\'t find a user with that e-mail address.'
                ], 404);
            }
    
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $user->email,
                'token' => str_random(60)
            ]);
    
            if ($user && $passwordReset) {
                $user->notify(
                    new PasswordResetRequest($passwordReset->token)
                );
            }
            
            DB::commit();
            return response()->json([
                'message' => 'We have e-mailed your password reset link!'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Can not create link, please try again!'
            ]);
        }
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token = null)
    {
        DB::beginTransaction();
        try {
            $passwordReset = PasswordReset::where('token', $token)->first();
            if (!$passwordReset) {
                return response()->json([
                    'message' => 'This password reset token is invalid.'
                ], 404);
            }

            if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset->delete();
                return response()->json([
                    'message' => 'This password reset token is invalid.'
                ], 404);
            }

            DB::commit();
            return response()->json($passwordReset);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'This password reset token is not exists.'
            ], 404);
        }
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(ApiPasswordResetRequest $request)
    {
        DB::beginTransaction();
        try {
            $conditions = [
                ['token', $request->token],
                ['email', $request->email]
            ];

            $passwordReset = PasswordReset::where($conditions)->first();
            if (!$passwordReset) {
                return response()->json([
                    'message' => 'This password reset token is invalid.'
                ], 404);
            }

            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'We can\'t find a user with that e-mail address.'
                ], 404);
            }

            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            $user->notify(new PasswordResetSuccess($passwordReset));

            DB::commit();
            return response()->json($user);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Fail to reset your password'
            ], 404);
        }
    }
}
