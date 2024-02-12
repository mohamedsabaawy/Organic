<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class PostController extends Controller
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
        $posts = Post::all();
        if (count($posts) > 0)
            return sendResponse(PostResource::collection($posts), 'all of posts', 1);
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
        if (!$post = Post::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(PostResource::make($post), 'successful', 1);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => "required",
            'title_en' => "required",
            'content' => "required",
            'content_en' => "required",
            'photo' => "required|image",
            'author' => "required",
            'author_en' => "required",
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $post = Post::create([
            'title' => $request->title,
            'title_en' => $request->title_en,
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'photo' => $request->photo->store('posts', 'public'),
        ]);

        return sendResponse(PostResource::make($post), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$post = Post::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id' => $post->id,
            'title' => $post->title,
            'title_en' => $post->title_en,
            'content' => $post->content,
            'content_en' => $post->content_en,
            'photo' => asset('photo/' . $post->photo),
            'author' => $post->author,
            'author_en' => $post->author,
        ], 'successful', 1);
    }

    public function update(Request $request, string $id)
    {
        if (!$post = Post::find($id))
            return sendResponse([], 'not found', 0);

        $validator = Validator::make($request->all(), [
            'title' => "required",
            'title_en' => "required",
            'content' => "required",
            'content_en' => "required",
            'photo' => "nullable|image",
            'author' => "required",
            'author_en' => "required",
        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }


        $file = $post->photo;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($item->photo ?? "d sdfs");
            $file = $request->photo->store('posts', 'public'); //تسجيل الصورة الجديدة
        }

        $post->update([
            'title' => $request->title,
            'title_en' => $request->title_en,
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'photo' => $file,
        ]);

        return sendResponse(PostResource::make($post), 'successful', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$post = Post::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($post->photo ?? "ddddsdfadsf");
        $post->delete();
        return sendResponse([], 'successful', 1);
    }
}
