<?php

declare(strict_types=1);

namespace Modules\Post\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Modules\Post\app\Models\Post;
use Modules\Post\app\Resources\PostResource;

final class PostController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(Post::paginate());
    }

    public function store(Request $request): PostResource
    {
        $request->validate([
            'title' => 'required|string',
            'schedule_at' => 'date|after:now',
        ]);

        $post = Post::create([
            'title' => $request->input('title'),
            'schedule_at' => $request->input('schedule_at'),
            'author_id' => $request->user()->id,
            'is_published' => !$request->input('schedule_at'),
        ]);

        return new PostResource($post);
    }

    public function show(int $id): PostResource
    {
        return new PostResource(Post::findOrFail($id));
    }

    public function update(Request $request, int $id): PostResource
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'schedule_at' => 'sometimes|date|after:now',
        ]);

        $post = Post::findOrFail($id, ['id', 'is_published']);

        if ($post->is_published && isset($validated['schedule_at'])) {
            throw ValidationException::withMessages([
                'schedule_at' => 'The post is already published.',
            ]);
        }

        $post->update($validated);

        return new PostResource($post);
    }

    public function destroy(int $id): Response
    {
        Post::findOrFail($id)->delete();

        return response()->noContent();
    }
}
