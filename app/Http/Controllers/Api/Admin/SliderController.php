<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class SliderController extends Controller
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
        $sliders = Slider::all();
        if (count($sliders) > 0)
            return sendResponse(SliderResource::collection($sliders), 'all of sliders', 1);
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
        if (!$slider = Slider::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(SliderResource::make($slider), 'successful', 1);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo'=>'required|image',
            'status'=>'required|in:active,non active',
            'title'=>'required',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $slider = Slider::create([
            'photo' => $request->photo->store('sliders','public'),
            'status' => $request->status,
            'title' => $request->title,
        ]);

        return sendResponse(SliderResource::make($slider), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$slider = Slider::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id'=>$slider->id,
            'photo'=>asset('photo/'.$slider->photo),
            'status'=>$slider->status,
        ], 'successful', 1);
    }

    public function update(Request $request,string $id)
    {
        if (!$slider = Slider::find($id))
            return sendResponse([], 'not found', 0);

        $validator = Validator::make($request->all(), [
            'photo'=>'required|image',
            'status'=>'required|in:active,non active',
            'title'=>'required',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }


        $file = $slider->photo;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($item->photo ??"d sdfs");
            $file = $request->photo->store('sliders', 'public'); //تسجيل الصورة الجديدة
        }

        $slider->update([
            'photo' => $file,
            'status' => $request->status,
            'title' => $request->title,
        ]);

        return sendResponse(SliderResource::make($slider), 'successful', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$slider = Slider::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($slider->photo ?? "ddddsdfadsf");
        $slider->delete();
        return sendResponse([], 'successful', 1);
    }
}
