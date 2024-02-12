<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HintResource;
use App\Models\Hint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\sendResponse;

class HintController extends Controller
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
        $hints = Hint::all();
        if (count($hints) > 0)
            return sendResponse(HintResource::collection($hints), 'all of hints', 1);
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
        if (!$post = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(HintResource::make($post), 'successful', 1);
    }

    public function store(Request $request)
    {
//        return $request->all();
        if (count($this->Valid($request)->errors()) > 0) {
            return sendResponse($this->Valid($request)->errors(), 'validation error', 0);
        }

        $post = Hint::create([
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'author_job' => $request->author_job,
            'author_job_en' => $request->author_job_en,
        ]);

        return sendResponse(HintResource::make($post), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$post = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id' => $post->id,
            'content' => $post->content,
            'content_en' => $post->content_en,
            'author' => $post->author,
            'author_en' => $post->author_en,
            'author_job' => $post->author_job,
            'author_job_en' => $post->author_job_en,
        ], 'successful', 1);
    }

    public function update(Request $request, string $id)
    {
        if (!$post = Hint::find($id))
            return sendResponse([], 'not found', 0);

        if (count($this->Valid($request)->errors()) > 0) {
            return sendResponse($this->Valid($request)->errors(), 'validation error', 0);
        }

        $post->update([
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'author_job' => $request->author_job,
            'author_job_en' => $request->author_job_en,
        ]);

        return sendResponse(HintResource::make($post), 'successful', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$post = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($post->photo ?? "ddddsdfadsf");
        $post->delete();
        return sendResponse([], 'successful', 1);
    }

    private function Valid($request){
        return  Validator::make($request->all(), [
            'content' => "required",
            'content_en' => "required",
            'author' => "required",
            'author_en' => "required",
            'author_job' => "required",
            'author_job_en' => "required",
        ]);
    }
}
