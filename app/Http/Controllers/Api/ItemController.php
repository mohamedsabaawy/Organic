<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ItemResource;
use App\Http\Resources\Api\PhotoResource;
use App\Models\Category;
use App\Models\Item;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class ItemController extends Controller
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
//        return request()->ipinfo->country;
        $items = Item::with('category')->where('available', 'active')->get();
        if (request()->get('special') == 1) //get special item
            $items = Item::with('category')->where('special', 1)->get();
        if (request()->get('available') == ('active' or 'nonActive')) //get active item or nonActive item
            $items = Item::with('category')->where('available', request()->get('available'))->get();
        if (request()->get('available') == 'all') //get all item
            $items = Item::with('category')->get();

        if (count($items) > 0)
            return sendResponse(ItemResource::collection($items), 'all of items', 1);
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $categories = Category::all()->pluck('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'name_en' => 'required',
            'details' => 'required',
            'details_en' => 'required',
            'icon' => 'required|image',
            'manual' => 'required',
            'manual_en' => 'required',
            'production_date' => 'required',
            'available' => 'required|in:"active","nonActive"',
            'price' => 'required|numeric|min:1',
            'price_dollar' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_dollar' => 'nullable|numeric',
            'special' => 'nullable|in:0,1',
            'category_id' => ['required', Rule::in($categories)],
            'photos.*' => 'required|image',
        ]);


        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $item = Item::create([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'details' => $request->details,
            'details_en' => $request->details_en,
            'icon' => $request->icon->store('items', 'public'),
            'manual' => $request->manual,
            'manual_en' => $request->manual_en,
            'production_date' => date("Y-m-d", strtotime($request->production_date)),
            'available' => $request->available,
            'price' => $request->price,
            'price_dollar' => $request->price_dollar,
            'discount' => $request->discount ?? 0,
            'discount_dollar' => $request->discount_dollar ?? 0,
            'category_id' => $request->category_id,
            'special' => $request->special,
        ]);

        if ($request->photos && count($request->photos) > 0)
            foreach ($request->photos as $photo) {
                $item->photos()->create([
                    'path' => $photo->store('items/' . $item->id, 'public'),
                    'type' => 1
                ]);
            }

        return sendResponse(ItemResource::make($item), 'successfully', 1);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($items = Item::with(['category', 'photos'])->find($id))
            return sendResponse(ItemResource::make($items), 'successful', 1);
        return sendResponse([], 'no date found', 1);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!$item = Item::with(['category', 'photos'])->find($id)) {
            return sendResponse([], 'not found', 0);
        }

        return sendResponse([
            'id' => $item->id,
            'name' => $item->name,
            'name_en' => $item->name_en,
            'details' => $item->details,
            'details_en' => $item->details_en,
            'manual' => $item->manual,
            'manual_en' => $item->manual_en,
            'price' => $item->price,
            'price_dollar' => $item->price_dollar,
            'discount' => $item->discount,
            'discount_dollar' => $item->discount_dollar,
            'percent' => number_format((($item->price - $item->discount) / $item->price) * 100, 1) . "%",
            'icon' => asset('photo/' . $item->icon),
            'available' => $item->available,
            'production_date' => $item->production_date,
            'special' => $item->special,
            'created_at' => date("Y-m-d", strtotime($item->created_at)),
            'updated_at' => date("Y-m-d", strtotime($item->updated_at)),
            'category' => [
                'id' => $item->category->id,
                'name' => $item->category->name,
            ],
            'photos' => PhotoResource::collection($item->photos),
        ], 'successfully', 1);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categories = Category::all()->pluck('id');
        if (!$item = Item::with('photos')->find($id))
            return sendResponse([], 'not found', 0);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'name_en' => 'required',
            'details' => 'required',
            'details_en' => 'required',
            'icon' => 'nullable|image',
            'manual' => 'required',
            'manual_en' => 'required',
            'production_date' => 'required',
            'available' => 'required|in:"active","nonActive"',
            'price' => 'required|numeric|min:1',
            'price_dollar' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_dollar' => 'nullable|numeric',
            'special' => 'nullable|in:0,1',
            'category_id' => ['required', Rule::in($categories)],
        ]);
        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $file = $item->icon;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('icon')) {
            Storage::disk('public')->delete($item->icon ?? "d sdfs");
            $file = $request->icon->store('items', 'public'); //تسجيل الصورة الجديدة
        }


        $item->update([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'details' => $request->details,
            'details_en' => $request->details_en,
            'icon' => $file,//
            'manual' => $request->manual,
            'manual_en' => $request->manual_en,
            'production_date' => date("Y-m-d", strtotime($request->production_date)),
            'available' => $request->available,
            'price' => $request->price,
            'price_dollar' => $request->price_dollar,
            'discount' => $request->discount ?? 0,
            'discount_dollar' => $request->discount_dollar ?? 0,
            'category_id' => $request->category_id,
            'special' => $request->special,
        ]);


        return sendResponse(ItemResource::make($item), 'successfully', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$item = Item::with(['photos', 'invoices'])->find($id))
            return sendResponse([], 'not found', 0);
        if (count($item->invoices) > 0) {
            $item->update(['available' => 'nonActive']);
            return sendResponse([], 'you can\'t delete this item as it has some orders and it now not active', 0);
        }
        $item->delete();
        Storage::disk('public')->delete($item->icon);
        DB::table('photos')->where('item_id', $id)->delete();
        File::deleteDirectory(public_path('photo/items/' . $id));
        return sendResponse([], 'delete item successfully', 1);
    }

    public function photoDelete($id)
    {
        if (!$photo = Photo::find($id))
            return sendResponse([], 'not found', 0);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return sendResponse([], 'successfully', 1);
    }

    public function addPhoto(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'photos.*' => 'required|image'
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        if (!$item = Item::find($id))
            return sendResponse([], 'not found', 0);

        foreach ($request->photos as $photo) {
            $item->photos()->create([
                'path' => $photo->store('items/' . $item->id, 'public'),
                'type' => 1
            ]);
        }

        return sendResponse([], 'successfully', 1);
    }

    public function makeSpecial($id)
    {
        if (!$item = Item::find($id))
            return sendResponse([], 'not found', 0);
//        return $item;
        $item->update([
            'special' => $item->special ? 0 : 1
        ]);
        return sendResponse(ItemResource::make($item), 'successfully', 1);
    }


}
