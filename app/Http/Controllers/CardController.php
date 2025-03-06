<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\StripeService;
use App\Repositories\CardRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
     * Register a new card.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'number' => 'required|string|min:15|max:16',
                'exp_month' => 'required|integer|min:1|max:12',
                'exp_year' => 'required|integer|min:2023',
                'cvc' => 'required|string|min:3|max:4',
            ]);

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
     * List user cards.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listCards(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            $user = $this->authService->validateToken($token);

            $cards = $this->cardRepository->getUserCards($user->id);

            $formattedCards = $cards->map(function ($card) {
                return [
                    'id' => $card->id,
                    'last_four' => $card->last_four,
                    'brand' => $card->brand,
                    'exp_month' => $card->exp_month,
                    'exp_year' => $card->exp_year,
                    'is_default' => $card->is_default,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Cards retrieved successfully',
                'data' => $formattedCards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Charge a card.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chargeCard(Request $request): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'card_id' => 'required|integer|exists:user_cards,id',
                'amount' => 'required|numeric|min:0.5',
                'currency' => 'sometimes|string|size:3',
                'description' => 'sometimes|string',
            ]);

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
