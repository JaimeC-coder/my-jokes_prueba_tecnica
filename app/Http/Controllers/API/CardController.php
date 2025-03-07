<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Card\CardChargeRequest;
use App\Http\Requests\Card\CardRegisterRequest;
use App\Http\Resources\Card\CardResourse;
use App\Services\AuthService;
use App\Services\StripeService;
use App\Repositories\CardRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Issuing\Card;

class CardController extends Controller
{
    protected $authService;
    protected $stripeService;
    protected $cardRepository;

    public function __construct(
        AuthService $authService,
        StripeService $stripeService,
        CardRepository $cardRepository
    ) {
        $this->authService = $authService;
        $this->stripeService = $stripeService;
        $this->cardRepository = $cardRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/register-card",
     *     tags={"Cards"},
     *     summary="Registrar una nueva tarjeta",
     *     description="Registrar una nueva tarjeta para el usuario autenticado",
     *     operationId="registerCard",
     *     security={{"api_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"number", "exp_month", "exp_year", "cvc"},
     *             @OA\Property(property="payment_method", type="string", example="pm_card_visa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Card registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Card registered successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to register card")
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
    public function register(CardRegisterRequest $request): JsonResponse
    {
        try {


            $token = $request->header('Authorization');
            $user = $this->authService->validateToken($token);

            $result = $this->stripeService->registerCard($user, $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Card registered successfully',
                'data' => $result['stripe_response']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/list-cards",
     *     tags={"Cards"},
     *     summary="Lista de tarjetas de usuario",
     *     description="Lista de todas las tarjetas para el usuario autenticado",
     *     operationId="listCards",
     *     security={{"api_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cards retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Cards retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="last_four", type="string", example="4242"),
     *                     @OA\Property(property="brand", type="string", example="visa"),
     *                     @OA\Property(property="exp_month", type="integer", example=12),
     *                     @OA\Property(property="exp_year", type="integer", example=2026),
     *                     @OA\Property(property="is_default", type="boolean", example=true)
     *                 )
     *             )
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
    public function listCards(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            $user = $this->authService->validateToken($token);

            $cards = $this->cardRepository->getUserCards($user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Cards retrieved successfully',
                'data' =>  CardResourse::collection($cards)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/charge-card",
     *     tags={"Cards"},
     *     summary="Cargar una tarjeta",
     *     description="Cargar una tarjeta registrada para el usuario autenticado",
     *     operationId="chargeCard",
     *     security={{"api_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"card_id", "amount"},
     *             @OA\Property(property="card_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="currency", type="string", example="usd"),
     *             @OA\Property(property="description", type="string", example="Test charge")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Card charged successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Card charged successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to charge card")
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
    public function chargeCard(CardChargeRequest $request): JsonResponse
    {
        try {
            // Validate request


            $token = $request->header('Authorization');
            $user = $this->authService->validateToken($token);

            // Get the card
            $card = $this->cardRepository->findByIdAndUser($request->card_id, $user->id);
            if (!$card) {
                throw new \Exception('Card not found or does not belong to user');
            }

            // Process the charge
            $result = $this->stripeService->chargeCard(
                $user,
                $card,
                $request->amount,
                $request->currency ?? 'usd',
                $request->description
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Card charged successfully',
                'data' => $result['stripe_response']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
