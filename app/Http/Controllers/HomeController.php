<?php


// app/Http/Controllers/API/HomeController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\JokeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    protected $authService;
    protected $jokeService;

    public function __construct(
        AuthService $authService,
        JokeService $jokeService
    ) {
        $this->authService = $authService;
        $this->jokeService = $jokeService;
    }

    /**
     * Get home data (random joke if user has card).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            $user = $this->authService->validateToken($token);

            // Check if user has card
            if (!$user->hasCards()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'PENDING_CARD',
                    'data' => null
                ]);
            }

            // User has card, get random joke
            $joke = $this->jokeService->getRandomJoke();

            return response()->json([
                'status' => 'success',
                'message' => 'Joke retrieved successfully',
                'data' => $joke
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
