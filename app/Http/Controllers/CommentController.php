<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a specific post.
     * We pass in the $postId instead of using route model binding for flexibility here.
     */
    public function index($postId)
    {
        $post = \App\Models\Post::findOrFail($postId);
        $comments = $post->comments()->with('user:id,name')->latest()->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $postId)
    {
        $post = \App\Models\Post::findOrFail($postId);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user:id,name')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return response()->json($comment->load('user:id,name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        // Ensure user owns the comment
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized actions. You can only edit your own comments.'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
         // Ensure user owns the comment
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized actions. You can only delete your own comments.'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
