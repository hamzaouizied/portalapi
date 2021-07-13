<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;


class AccountController extends Controller
{
    /**
     * @param $email
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postLogin($email)
    {
        $client = new Client();
        try {
            $response = $client->request('POST', 'http://127.0.0.1:8000/api/login', [
                'form_params' => [
                    'email' => $email,
                ]
            ]);
            $response->getStatusCode();
            $response->getHeaderLine('content-type');
            $response->getBody();
            return
                [
                    'code' => $response->getStatusCode(),
                    'data' => json_decode($response->getBody()->getContents(), true),
                ];
        }catch(\GuzzleHttp\Exception\RequestException  $e) {
            $response = $e->getResponse();
            return  [
                'message' => $response->getReasonPhrase(),
                'code'    => $response->getStatusCode(),
            ];
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLogin()
    {
        return view('account.login.index');
    }

    /**
     * @param $token
     * @param $uuid
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifyLinkAuth($token, $uuid, $id)
    {
        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'uuid'          => $uuid,
            'Accept'        => 'application/json',
        ];

        try {
            $response = $client->request('POST', 'http://127.0.0.1:8000/api/verify/'.$id, [
                'headers' => $headers
            ]);
            $response->getStatusCode();
            $response->getHeaderLine('content-type');
            $response->getBody();
            return
                [
                    'code' => $response->getStatusCode(),
                    'data' => json_decode($response->getBody()->getContents(), true),
                ];
        }catch(\GuzzleHttp\Exception\RequestException  $e) {

            $response = $e->getResponse();
            return  [
                'message' => $response->getReasonPhrase(),
                'code'    => $response->getStatusCode(),
            ];
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(Request $request)
    {
        $request->validate([
           'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha'

        ]);

        $data = $this->postLogin($request->email);

        if(count($data) > 0)
        {
            if($data['code'] === 401){
                return view('account.login.index', [
                   'error' =>  'You are not authorized'
                ]);
            }else if($data['code'] === 200){
                $request->session()->put('token', $data['data']['token']);
                $request->session()->put('uuid', $data['data']['UUID']);
                return view('account.login.index', [
                    'success' =>  'Check your email for access link'
                ]);
            }
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAuth(Request $request, $id)
    {
        $uuid  = $request->session()->get('uuid');
        $token = $request->session()->get('token');
        if($uuid && $token){
            $checkAith = $this->verifyLinkAuth($token, $uuid, $id);

            if($checkAith['code'] === 200){
                $request->session()->put('id', $checkAith['data']['id']);
                return view('account.dashboard.index', [
                    'email' => $checkAith['data']['email'],
                    'id'    => $checkAith['data']['id']
                ]);
            }
        }
        return redirect('/login')->with('error', ['session expired please try again']);
    }

    /**
     * @param $email
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser($token, $uuid, $id, $email)
    {

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'uuid'          => $uuid,
            'Accept'        => 'application/json',
        ];

        try {
            $response = $client->request('POST', 'http://127.0.0.1:8000/api/update/'.$id, [
                'headers' => $headers,
                'form_params' => [
                    'email' => $email,
                ]
            ]);
            $response->getStatusCode();
            $response->getHeaderLine('content-type');
            $response->getBody();
            return
                [
                    'code' => $response->getStatusCode(),
                    'data' => json_decode($response->getBody()->getContents(), true),
                ];
        }catch(\GuzzleHttp\Exception\RequestException  $e) {

            $response = $e->getResponse();
            return  [
                'message' => $response->getReasonPhrase(),
                'code'    => $response->getStatusCode(),
            ];
        }
    }

    /**
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $id    = $request->session()->get('id');
        $uuid  = $request->session()->get('uuid');
        $token = $request->session()->get('token');

        $updated = $this->updateUser($token, $uuid, $id, $request->email);
        if($updated['code'] === 200)
        {
            return response()->json(
                [   'message'  => "successfully updated",
                    'code'     => 200 ,
                ], 200);
        }
        return response()->json(
            [   'message'  => "something wrong",
                'code'     => 500 ,
            ], 500);

    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function logout($token, $uuid, $id)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'uuid'          => $uuid,
            'Accept'        => 'application/json',
        ];

        try {
            $response = $client->request('POST', 'http://127.0.0.1:8000/api/logout', [
                'headers' => $headers,
                'form_params' => [
                    'id' => $id,
                ]
            ]);
            $response->getStatusCode();
            $response->getHeaderLine('content-type');
            $response->getBody();
            return
                [
                    'code' => $response->getStatusCode(),
                    'data' => json_decode($response->getBody()->getContents(), true),
                ];
        }catch(\GuzzleHttp\Exception\RequestException  $e) {

            $response = $e->getResponse();
            return  [
                'message' => $response->getReasonPhrase(),
                'code'    => $response->getStatusCode(),
            ];
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userLogout(Request $request)
    {
        $id    = $request->session()->get('id');
        $uuid  = $request->session()->get('uuid');
        $token = $request->session()->get('token');

        $logout = $this->logout($token, $uuid, $id);
        if($logout['code'] === 200)
        {
            $request->session()->forget('id');
            $request->session()->forget('uuid');
            $request->session()->forget('token');
            return response()->json(
                [   'message'  => "successfully logout",
                    'code'     => 200 ,
                ], 200);
        }
        return response()->json(
            [   'message'  => "something wrong",
                'code'     => 500 ,
            ], 500);


    }
}


