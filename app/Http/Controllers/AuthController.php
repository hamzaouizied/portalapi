<?php

namespace App\Http\Controllers;

use App\Mail\UserAuth;
use App\Token;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\ThrottleRequestsException;





class AuthController extends Controller
{
    /**
     * AuthController constructor..
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $user = User::where('email', '=', $request->email)->first();
            if ($user != NULL) {
                $token = $user->createToken('userAccess')->accessToken;
                $uuid  = Str::uuid()->toString();
                $link  = sha1($user->id);
                Token::create([
                    'token'           => $token,
                    'uuid'            => $token,
                    'temporary_link'  => $link,
                    'user_id'         => $user->id
                ]);
                Mail::to($request->email)->send(new UserAuth($user->name, env('APP_URL_FRONT').'/verify/'.$link));
                return response()->json(['token' => $token, 'UUID' => $uuid], 200);
            } else {
                return response()->json(['error' => 'UnAuthorised'], 401);
            }
        } catch(\Exception $e){
            $status = HttpResponse::HTTP_BAD_REQUEST;
            return response()->json(['message' => $e->getMessage(), 'status ' => $status], $status);
        }
    }

    /**
     * @param Request $request
     */
    public function authUser($userLink)
    {
        try {
            $checkAuth = Token::with('user')->whereHas('user')
                ->where('temporary_link', '=', $userLink)
                ->first();
            if ($checkAuth) {
                return response()->json(['message' => 'authenticated user', 'status' => 200, 'email' =>$checkAuth->user->email, 'id' => $checkAuth->user->id], 200);

            } else {
                return response()->json(['error' => 'UnAuthorised'], 401);
            }
        }catch(\Exception $e){
            $status = HttpResponse::HTTP_BAD_REQUEST;
            return response()->json(['message' => $e->getMessage(), 'status ' => $status], $status);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        try {
           User::where('id', $request->id)->update(['email' => $request->email]);
            return response()->json(['message' => 'user update successfully'], 200);
        } catch(\Exception $e){
            $status = HttpResponse::HTTP_BAD_REQUEST;
            return response()->json(['message' => $e->getMessage(), 'status ' => $status], $status);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut(Request $request)
        {

            try {
                Token::where('user_id', $request->id)->update(['token' => NULL, 'uuid' => NULL, 'temporary_link' => NULL]);
                return response()->json(['message' => 'user logout'], 200);
            } catch(\Exception $e){
                $status = HttpResponse::HTTP_BAD_REQUEST;
                return response()->json(['message' => $e->getMessage(), 'status ' => $status], $status);
            }
        }

}
