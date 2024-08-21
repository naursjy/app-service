<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        // $user = Auth::user();
        // $posts = Post::with('user')->get();
        return new PostResource(true, 'List Data Posts', $posts);
    }


    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        $user = Auth::user();
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        return new PostResource(['user_id' => $user->id], 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        $post = Post::find($id);
        $user = Auth::user();
        if ($user) {
            return new PostResource(['user_id' => $user->id], 'Detail Data Post!', $post);
        } else {
            return new PostResource(false, 'gagal', 401);
        }


        // return new PostResource(true, 'Detail Data Post!', $post);
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);

        if ($request->hasFile('image')) {

            //uploud gambar
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts', basename($post->image));

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        return new PostResource(['user_id' => $user->id], 'Data Berhasil Diubah!', $post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $user = Auth::user();
        Storage::delete('public/posts/' . basename($post->image));

        $post->delete();
        return new PostResource(['user_id' => $user->id], 'Data Berhasil Dihapus', null);
    }
}
