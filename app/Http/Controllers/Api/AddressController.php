<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AddressResource;
use App\Models\Address;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function included\sendResponse;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::where('client_id', Auth::guard('api')->id())->get();
        if (count($addresses) > 0)
            return sendResponse(AddressResource::collection($addresses), 'successful', 1);
        return sendResponse([], 'sorry no data found', 1);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($address = Address::find($id))
            return sendResponse(AddressResource::make($address), 'successful', 1);
        return sendResponse([], 'sorry no data found', 1);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'phone' => 'required',
            'street_name' => 'required',
            'build_name' => 'required',
            'city' => 'required',
            'government' => 'required',
            'landmark' => 'required',

        ]);
        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        if (!$client = Client::find(Auth::guard('api')->id()))
            return sendResponse([],'try again',0);


        $address = $client->addresses()->create([
            'phone' => $request->phone,
            'street_name' => $request->street_name,
            'build_name' => $request->build_name,
            'city' => $request->city,
            'government' => $request->government,
            'landmark' => $request->landmark,
        ]);
        if (!$address)
            return sendResponse([],'try again',0);
        return sendResponse(AddressResource::make($address),'successful',1);
    }

    public function update(Request $request,$id)
    {

        if (!$address = Address::find($id))
            return sendResponse([],'this address not found , try again',0);

        $validator = Validator::make($request->all(), [
//            'phone' => 'required',
            'street_name' => 'required',
            'build_name' => 'required',
            'city' => 'required',
            'government' => 'required',
            'landmark' => 'required',

        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $update = $address->update([
            'phone' => $request->phone,
            'street_name' => $request->street_name,
            'build_name' => $request->build_name,
            'city' => $request->city,
            'government' => $request->government,
            'landmark' => $request->landmark,
        ]);
        if (!$update)
            return sendResponse([],'try again',0);
        return sendResponse(AddressResource::make($address),'successful',1);

    }

    public function destroy($id)
    {
        if (!$address = Address::find($id))
            return sendResponse([],'this address is incorrect',0);
        if ($address->delete())
            return sendResponse([],'delete successfully',1);

    }
}
