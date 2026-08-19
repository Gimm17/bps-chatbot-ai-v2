<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requestId' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'string', 'in:helpful,not_helpful'],
            'comment' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::table('feedback')->insert([
                'request_id' => $validated['requestId'] ?? null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::info('Feedback received (db fallback log): '.json_encode($validated));
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Terima kasih atas masukan Anda!',
        ]);
    }
}
