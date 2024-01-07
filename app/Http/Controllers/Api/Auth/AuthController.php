<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function included\sendResponse;

class AuthController extends Controller
{
    /**/
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => "required|email|unique:clients",
            'password' => "required|min:6",
        ]);
        if ($validator->errors()) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }
        /*
         * create new user
         * */
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        /*
         * login after register
         * */
        $credentials = request(['email', 'password']);
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendResponse('', 'you have entered invalid data');
        }
        return sendResponse(['token' => $token], 'register successful', 1);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendResponse(['token' => $token], 'please enter valid credentials', 0);
        }

        return sendResponse(['token' => $token], 'login successful', 1);

    }
//    public function login(Request $request){
//        $request->validate([
//            'email'=>"required|email",
//            'password'=>"required",
//        ]);
//
//        $client = Client::where('email',$request->email)->first();
//        if (!$client){
//            return response()->json([
//                'msg'=>'لقد ادخلت معلومات غير صحيحة'
//            ],403);
//        }
//        if (!Hash::check($request->password,$client->password)){
//            return response()->json([
//                'msg'=>'لقد ادخلت معلومات غير صحيحة'
//            ],403);
//        }
//        $token =  $client->createToken('client auth')->plainTextToken;
//        return response()->json([
//            'msg'=>' تم تسجيل الدخول بنجاح',
//            'token'=>$token,
//        ],200);
//    }

    public function show()
    {
        return sendResponse(['user' => Auth::guard('api')->user()], 'successful', 1);
    }

}
