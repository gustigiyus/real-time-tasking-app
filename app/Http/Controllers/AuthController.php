<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Events\NewUserCreated;

class AuthController extends Controller
{
    private $secretkey = 'vdOgkstZSM2IGAIZ8oeeVXmaMzHzOgFbVanEYwn7P0Rq3D';

    public function register(request $request)
    {
        $fields = $request->all();
        $errors = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required|min:6|max:8',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::create([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'isValidEmail' => User::IS_INVALID_EMAIL,
            'remember_token' => $this->generateRandomCode(),
        ]);

        NewUserCreated::dispatch($user);

        return response(['user' => $user, 'message' => 'user created'], 200);
    }

    public function validEmail($token)
    {
        User::where('remember_token', $token)
            ->update(['isValidEmail' => User::IS_VALID_EMAIL]);

        return redirect('/login');
    }


    function generateRandomCode()
    {
        $code = Str::random(10) . time();
        return $code;
    }

    public function login(request $request)
    {
        $fields = $request->all();
        $errors = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::where('email', $fields['email'])->first();

        if (!is_null($user)) {
            if (intval($user->isValidEmail) !== User::IS_VALID_EMAIL) {
                return response(['message' => 'We send you an email verification!']);
            }
        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'email or password invalid!', 'isLoggedIn' => true], 422);
        }

        $token = $user->createToken($this->secretkey)->plainTextToken;

        return response([
            'user' => $user,
            'message' => 'loggedin',
            'token' => $token,
            'isLoggedIn' => true
        ], 200);
    }
}
