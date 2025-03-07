<?php

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
     * @OA\Get(
     *     path="/api/home",
     *     tags={"Home"},
     *     summary="Obtener datos de inicio",
     *     description="Obtener broma al azar si el usuario tiene tarjeta",
     *     operationId="home",
     *     security={{"api_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="status", type="string", example="success"),
     *                     @OA\Property(property="message", type="string", example="Joke retrieved successfully"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="category", type="string", example="Programming"),
     *                         @OA\Property(property="type", type="string", example="single"),
     *                         @OA\Property(property="joke", type="string", example="Why do programmers always mix up Halloween and Christmas? Because Oct 31 == Dec 25.")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="status", type="string", example="warning"),
     *                     @OA\Property(property="message", type="string", example="PENDING_CARD"),
     *                     @OA\Property(property="data", type="null", example=null)
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="INVALID TOKEN")
     *         )
     *     )
     * )
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
