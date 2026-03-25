<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Generate product details using OpenAI based on a title.
     */
    public function generateWithAI(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $title = $request->input('title');
        $apiKey = config('services.openrouter.api_key');
        $baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');

        if (!$apiKey) {
            return response()->json([
                'error' => 'OpenRouter API key is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => config('app.url', 'http://localhost'),
                'X-Title'       => config('app.name', 'AI Automation'),
            ])->timeout(30)->post($baseUrl . '/chat/completions', [
                'model'       => 'openai/gpt-4o-mini',
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a product listing expert. Given a product title, generate realistic and detailed product information. Respond ONLY with a valid JSON object (no markdown, no code fences) with these exact keys: description (2-3 sentences), category (single category name), price (number only, realistic USD price), sku (alphanumeric SKU code like "WBH-2024-001"), brand (realistic brand name), tags (comma-separated string of 4-5 tags), features (string with 4-5 bullet points separated by newlines, each starting with "• "), meta_title (SEO title under 60 chars), meta_description (SEO description under 155 chars).'
                    ],
                    [
                        'role'    => 'user',
                        'content' => 'Generate product details for: ' . $title
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens'  => 600,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $productData = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'error' => 'Failed to parse AI response.'
                    ], 500);
                }

                return response()->json($productData);
            }

            return response()->json([
                'error' => 'OpenAI API request failed: ' . $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
