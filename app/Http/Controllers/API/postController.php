<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\post;

class postController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = post::all();
        return response()->json([
            'status' => true,
            'data' => $data
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = Validator::make(
            $request->all(),[
                'title' => 'required',
                'discreption' => 'required',
                'image' => 'required|mimes : jpg,jpeg,gif,png',
            ]
        );

        if($validateData->fails()){
            return response()->json([
                'status' => false,
                'error' => $validateData->error()->all()
            ],404);
        }

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time(). "." .$ext;
        $image->move(public_path().'/uploads' , $imageName);

        $post = post::create([
            'title' => $request->title,
            'discreption' => $request->discreption,
            'image' => $imageName
        ]);

        return response()->json([
            'status' => true,
            'user' => $post
        ],202);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = post::select('title','discreption','image')->where(['id'=>$id])->get();
        return response()->json([$data],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = Validator::make(
            $request->all(),[
                'title' => 'required',
                'discreption' => 'required',
                'image' => 'required|mimes : jpg,jpeg,gif,png',
            ]
        );

        if($validateData->fails()){
            return response()->json([
                'status' => false,
                'error' => $validateData->error()->all()
            ],404);
        }

        $post = post::select('id', 'image')->get();
        if($request->image != ''){
            $imagepath = public_path('')."/uploads";
            if($post->image != '' && $post->image != null){
                $old_file = $imagepath. $post->image;
                if(file_exists($old_file)){
                    unlink($old_file);
                }
            }
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time(). "." .$ext;
            $image->move(public_path().'./uploads'.$imageName);
        }else{
            $imageName = $post->image;
        }

        $post = post::where(['id' => $id])->update([
            'title' => $request->title,
            'discreption' => $request->discreption,
            'image' => $imageName
        ]);

        return response()->json([
            'status' => true,
            'user' => $post,
            'message' => 'post update sucessfully'
        ],202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = post::select('image')->where('id',$id)->get();
        $filePath = public_path(). './uploads' .$imagePath[0]['image'];
        unlink($filePath);

        $post = post::where('id',$id)->delete();
        return response()->json([
            'message' => 'delete sucessfully',
        ],200);
    }
}
