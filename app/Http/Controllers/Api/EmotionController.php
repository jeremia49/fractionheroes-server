<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmotionController extends Controller
{
    public function detect(Request $request)
    {
        $imageBinary = $request->getContent();

        if (empty($imageBinary)) {
            return response('no_image', 400)->header('Content-Type', 'text/plain');
        }

        $key = env('FACEPP_API_KEY');
        $secret = env('FACEPP_API_SECRET');
        $url = env('FACEPP_DETECT_URL', 'https://api-us.faceplusplus.com/facepp/v3/detect');

        if (!$key || !$secret || !$url) {
            return response('server_not_configured', 500)
                ->header('Content-Type', 'text/plain');
        }

        // Face++ Detect API expects multipart form-data with image_file
        $resp = Http::timeout(14)
            ->asMultipart()
            ->attach('image_file', $imageBinary, 'image.jpg')
            ->post($url, [
                'api_key' => $key,
                'api_secret' => $secret,
                'return_attributes' => 'emotion',
            ]);

        if (!$resp->successful()) {
            // Return Face++ error text for debugging (still plain text)
            return response('facepp_error_' . $resp->status(), 502)
                ->header('Content-Type', 'text/plain');
        }

        $json = $resp->json();

        // No face found
        if (
            !isset($json['faces']) ||
            !is_array($json['faces']) ||
            count($json['faces']) === 0
        ) {
            return response('No faces detected', 200)->header('Content-Type', 'text/plain');
        }

        // Take first face
        $emotion = $json['faces'][0]['attributes']['emotion'] ?? null;
        if (!$emotion || !is_array($emotion)) {
            return response('No faces detected', 200)->header('Content-Type', 'text/plain');
        }

        // Face++ emotion keys usually include:
        // anger, disgust, fear, happiness, neutral, sadness, surprise
        // You want: happy, surprised, angry, disgust, contempt, fear, neutral
        //
        // Note: Face++ often does NOT include "contempt". If it's missing, we output none.
        // We also ignore sadness (or you can map sadness->neutral if you want).
        $map = [
            'happiness' => 'happy',
            'surprise' => 'surprise',
            'neutral' => 'neutral',
            'sadness' => 'sad',
            'anger' => 'angry',
            'disgust' => 'disgust',
            'fear' => 'fear',
            'contempt' => 'contempt', // only if Face++ returns it (many times it won't)
        ];

        $bestLabel = 'neutral';
        $bestScore = -INF;

        foreach ($map as $faceppKey => $yourLabel) {
            if (isset($emotion[$faceppKey])) {
                $score = (float) $emotion[$faceppKey];
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestLabel = $yourLabel;
                }
            }
        }

        return response($bestLabel, 200)->header('Content-Type', 'text/plain');
    }
}