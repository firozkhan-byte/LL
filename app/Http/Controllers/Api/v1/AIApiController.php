<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIApiController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get machine learning sales forecasts.
     */
    public function getForecast(): JsonResponse
    {
        $forecast = $this->aiService->getSalesForecast();

        return response()->json([
            'success' => true,
            'data' => $forecast,
        ]);
    }

    /**
     * Post a question text to process natural language chat queries.
     */
    public function chatQuery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $reply = $this->aiService->processChatQuery($validated['message']);

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }
}
