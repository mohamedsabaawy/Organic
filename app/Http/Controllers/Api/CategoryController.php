<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use function included\sendResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('items')->get();
        if (count($categories)>0)
            return sendResponse(CategoryResource::collection($categories),'successful',1);
        return sendResponse([],'sorry no data found',1);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($category = Category::find($id))
            return sendResponse(CategoryResource::make($category),'successful',1);
        return sendResponse([],'sorry no data found',1);
    }
}
