<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::with('users:id,name')->latest();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        return response()->json($query->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'genre' => 'required|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:2030',
            'description' => 'nullable|string',
        ]);

        $media = Media::create($validated);

        return response()->json([
            'message' => 'Media berhasil ditambahkan',
            'data' => $media,
        ], 201);
    }

    public function show($id)
    {
        $media = Media::with('users:id,name')->find($id);

        if (!$media) {
            return response()->json([
                'message' => 'Media tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'data' => $media,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'message' => 'Media tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:100',
            'genre' => 'sometimes|required|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:2030',
            'description' => 'nullable|string',
        ]);

        $media->update($validated);

        return response()->json([
            'data' => $media,
        ], 200);
    }

    public function destroy($id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'message' => 'Media tidak ditemukan',
            ], 404);
        }

        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus.',
        ], 200);
    }
}