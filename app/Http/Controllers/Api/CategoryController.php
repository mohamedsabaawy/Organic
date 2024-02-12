<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function included\sendResponse;

class CategoryController extends Controller
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
        $categories = Category::with('items')->get();
        if (count($categories) > 0)
            return sendResponse(CategoryResource::collection($categories), 'successful', 1);
        return sendResponse([], 'sorry no data found', 1);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$category = Category::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(CategoryResource::make($category), 'successful', 1);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'name_en' => 'required',
            'photo' => 'required|image',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $category = Category::create([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'photo' => $request->photo->store('categories', 'public'),
        ]);
        return sendResponse(CategoryResource::make($category), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$category = Category::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id'=>$category->id,
            'photo'=>asset('photo/'.$category->photo),
            'name'=>$category->name,
            'name_en'=>$category->name_en,
        ], 'successful', 1);
    }

    public function update(Request $request, string $id)
    {
        if (!$category = Category::find($id))
            return sendResponse([], 'sorry no data found', 1);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'name_en' => 'required',
            'photo' => 'nullable|image',
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $file = $category->photo;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($category->photo ?? "adsfasd f");
            $file = $request->photo->store('categories', 'public'); //تسجيل الصورة الجديدة
        }

        $category->update([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'photo' => $file,
        ]);
        return sendResponse(CategoryResource::make($category), 'successfully', 1);

    }

    public function destroy(string $id)
    {
        if (!$category = Category::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($category->photo ?? "ddddsdfadsf");
        $category->delete();
        return sendResponse([], 'successful', 1);
    }
}
