<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function included\sendResponse;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::where('client_id',Auth::guard('api')->id())->get();
        if (count($addresses)>0)
            return sendResponse(AddressResource::collection($addresses),'successful',1);
        return sendResponse([],'sorry no data found',1);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($address = Address::find($id))
            return sendResponse(AddressResource::make($address),'successful',1);
        return sendResponse([],'sorry no data found',1);
    }

    public function store(Request $request){

    }
}
