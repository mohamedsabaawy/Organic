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
        if (!$hint = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse(HintResource::make($hint), 'successful', 1);
    }

    public function store(Request $request)
    {
//        return $request->all();
        if (count($this->Valid($request)->errors()) > 0) {
            return sendResponse($this->Valid($request)->errors(), 'validation error', 0);
        }

        $hint = Hint::create([
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'author_job' => $request->author_job,
            'author_job_en' => $request->author_job_en,
            'photo' => $request->photo->store('hints', 'public'),
        ]);

        return sendResponse(HintResource::make($hint), 'successful', 1);
    }

    public function edit($id)
    {
        if (!$hint = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        return sendResponse([
            'id' => $hint->id,
//            'content' => $hint->content,
//            'content_en' => $hint->content_en,
//            'author' => $hint->author,
//            'author_en' => $hint->author_en,
//            'author_job' => $hint->author_job,
//            'author_job_en' => $hint->author_job_en,
            'photo' => $hint->photo,
        ], 'successful', 1);
    }

    public function update(Request $request, string $id)
    {
        if (!$hint = Hint::find($id))
            return sendResponse([], 'not found', 0);

        if (count($this->Valid($request,"nullable")->errors()) > 0) {
            return sendResponse($this->Valid($request)->errors(), 'validation error', 0);
        }


        $file = $hint->photo;//تخزين المسار الحالي للصورة
//        التاكد من وجود صورة
        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($hint->photo ?? "no");
            $file = $request->photo->store('hints', 'public'); //تسجيل الصورة الجديدة
        }

//        dd($file);

        $hint->update([
            'content' => $request->content,
            'content_en' => $request->content_en,
            'author' => $request->author,
            'author_en' => $request->author_en,
            'author_job' => $request->author_job,
            'author_job_en' => $request->author_job_en,
            'photo' => $file,
        ]);

        return sendResponse(HintResource::make($hint), 'successful', 1);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$hint = Hint::find($id))
            return sendResponse([], 'sorry no data found', 1);
        Storage::disk('public')->delete($hint->photo ?? "no");
        $hint->delete();
        return sendResponse([], 'successful', 1);
    }

    private function Valid($request,$photo='required'){
        return  Validator::make($request->all(), [
//            'content' => "required",
//            'content_en' => "required",
//            'author' => "required",
//            'author_en' => "required",
//            'author_job' => "required",
//            'author_job_en' => "required",
            'photo' => "required|image",
        ]);
    }
}
