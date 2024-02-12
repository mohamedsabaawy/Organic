<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Http\Resources\Api\FavoriteResource;
use App\Http\Resources\Api\ItemResource;
use App\Http\Resources\Api\OfferResource;
use App\Models\Cart;
use App\Models\Client;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        if (!count($favorites = Favorite::where('client_id',auth('api')->id())->get()))
            return sendResponse([],'no data found',0);
        return sendResponse(
            FavoriteResource::collection($favorites),'successful',1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => [$request->offer_id ? 'nullable' : 'required', Rule::in(Item::all()->pluck('id'))],
            'offer_id' => [$request->item_id ? 'nullable' : 'required', Rule::in(Offer::all()->pluck('id'))],
        ]);
        if (count($validator->errors()) > 0)
            return sendResponse($validator->errors(), 'validation error', 0);
        $favorite = Client::find(Auth::id());
        if ($request->item_id)
            $favorite->favorites()->toggle($request->item_id);
        if ($request->offer_id)
            $favorite->offerFavorites()->toggle($request->offer_id);
        return sendResponse([],'successful',1);
    }

}
