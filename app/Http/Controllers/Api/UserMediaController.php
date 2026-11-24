<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class UserMediaController extends Controller
{
    public function index(Request $request)
    {
        $watchlist = $request->user()->media()->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar media user berhasil diambil',
            'data' => $watchlist,
        ], 200);
    }

    public function attach(Request $request)
    {
        $validated = $request->validate([
            'media_id' => 'required|exists:media,id',
            'status' => 'nullable|in:watchlist,watching,completed',
        ]);

        $user = $request->user();
        $status = $validated['status'] ?? 'watchlist';

        $user->media()->syncWithoutDetaching([
            $validated['media_id'] => ['status' => $status],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Media berhasil disimpan ke daftar user',
            'data' => $user->media()->where('media.id', $validated['media_id'])->first(),
        ], 201);
    }

    public function updateStatus(Request $request, $mediaId)
    {
        $validated = $request->validate([
            'status' => 'required|in:watchlist,watching,completed',
        ]);

        $user = $request->user();

        if (!$user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Media tidak ada di daftar user',
            ], 404);
        }

        $user->media()->updateExistingPivot($mediaId, [
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status tontonan berhasil diperbarui',
            'data' => $user->media()->where('media.id', $mediaId)->first(),
        ], 200);
    }

    public function detach(Request $request, $mediaId)
    {
        $user = $request->user();

        if (!$user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Media tidak ada di daftar user',
            ], 404);
        }

        $user->media()->detach($mediaId);

        return response()->json([
            'status' => true,
            'message' => 'Media berhasil dihapus dari daftar user',
        ], 200);
    }
}