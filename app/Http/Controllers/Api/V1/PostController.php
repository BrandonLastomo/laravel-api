<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $posts = $user->posts()->get();
        return PostResource::collection($posts); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $data['author_id'] = $request->user()->id;

        $post = Post::create($data);

        return response()->json(new PostResource($post), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        abort_if (Auth::id() != $post->author_id, 403, 'forbidden');
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        abort_if (Auth::id() != $post->author_id, 403, 'forbidden');
        $data=$request->validated();
        $post->update($data);

        return response()->json(new PostResource($post));
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if (Auth::id() != $post->author_id, 403, 'forbidden');
        $post->delete();
        return response()->noContent();
    }
}
