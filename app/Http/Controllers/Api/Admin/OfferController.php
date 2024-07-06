<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class OfferController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', 'CheckAdmin'], ['only' => [
            'store', 'update', 'delete'
        ]
        ]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::all();
        if ($available=request()->get('available')  and strlen(request()->get('available')) > 0)
            $offers = $offers->where('available', $available);
        if (count($offers) > 0)
            return sendResponse(OfferResource::collection($offers), 'all of offers', 1);
        return sendResponse([], 'sorry no date found', 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$offer = Offer::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(OfferResource::make($offer), 'successful', 1);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'required|image',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'available' => 'required|in:active,close',
            'price' => 'required|numeric|min:1',
            'price_dollar' => 'required|numeric|min:1',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $offer = Offer::create([
            'to' => date("Y-m-d", strtotime($request->to)),
            'from' => date("Y-m-d", strtotime($request->from)),
            'available' => $request->available,
            'price' => $request->price,
            'price_dollar' => $request->price_dollar,
            'icon' => $request->icon->store('offers', 'public'),
        ]);

        return sendResponse(OfferResource::make($offer), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$offer = Offer::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id' => $offer->id,
            'icon' => asset('photo/' . $offer->icon),
            'from' => $offer->from,
            'to' => $offer->to,
            'available' => $offer->available,
            'price' => $offer->price,
            'price_dollar' => $offer->price_dollar,
        ], 'successful', 1);
    }

    public function update(Request $request, string $id)
    {
        if (!$offer = Offer::find($id))
            return sendResponse([], 'not found', 0);

        $validator = Validator::make($request->all(), [
            'icon' => 'nullable|image',
            'from' => 'required|date',
            'to' => 'required|date',
            'available' => 'required|in:active,close',
            'price' => 'required|numeric|min:1',
            'price_dollar' => 'required|numeric|min:1',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }


        $file = $offer->icon;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('icon')) {
            Storage::disk('public')->delete($item->icon ?? "d sdfs");
            $file = $request->icon->store('offers', 'public'); //تسجيل الصورة الجديدة
        }

        $offer->update([
            'to' => date("Y-m-d", strtotime($request->to)),
            'from' => date("Y-m-d", strtotime($request->from)),
            'available' => $request->available,
            'price' => $request->price,
            'price_dollar' => $request->price_dollar,
            'icon' => $file,
        ]);

        return sendResponse(OfferResource::make($offer), 'successful', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$offer = Offer::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($offer->icon ?? "ddddsdfadsf");
        $offer->delete();
        return sendResponse([], 'successful', 1);
    }
}
