<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class AuthController extends Controller
{
    /**/
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => "required|email|unique:clients",
            'role' => "required|in:admin,user",
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
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);
        /*
         * login after register
         * */
        $credentials = request(['email', 'password']);
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendResponse('', 'you have entered invalid data');
        }
        return sendResponse(ClientResource::make($client), 'add successful', 1);
    }

    public function index(Request $request)
    {
        if($request->role == 'all') {
            $clients = Client::with('addresses')->get();
        }else
            $clients = Client::where('role',$request->role)->get();
        return sendResponse(ClientResource::collection($clients),'successful',1);
    }

    public function show($id)
    {
        $client = Client::with('addresses')->find($id);
        return sendResponse(ClientResource::make($client), 'successful', 1);
    }

    public function update(Request $request,$id)
    {
        if (! $client = Client::with('addresses')->find($id)){
            return sendResponse('','not found',0);
        }

        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => ["required", Rule::unique('clients')->ignore($id)],
            'role' => ["required"],
            'phone' => "nullable|min:11",
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
         * update user
         * */
        $update = $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => $request->password ? bcrypt($request->password) : $client->password,
        ]);
        if ($update)
            return sendResponse(ClientResource::make($client), 'successful', 1);
        return sendResponse([], 'please try again', 0);

    }

    public function delete($id)
    {
        if (!$client = Client::find($id))
            return sendResponse('','not found',0);
        $client->delete();
        return sendResponse('','successful',1);
    }

    public function swap($id){//swap
        if (!$client = Client::find($id))
            return sendResponse([],'you have entered invalid id',0);
        $client->role = $client->role == "admin" ? "user" : "admin";
        $client->save();
        return sendResponse(ClientResource::make($client),"successful",1);
    }

}
