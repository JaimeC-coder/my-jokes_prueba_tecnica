<?php
namespace App\Services;

use App\Models\Configurations;
use App\Repositories\UserRepository;
use App\Repositories\CardRepository;
use App\Repositories\ChargeRepository;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Charge;
use Exception;

class StripeService
{
    protected $userRepository;
    protected $cardRepository;
    protected $chargeRepository;

    public function __construct(
        UserRepository $userRepository,
        CardRepository $cardRepository,
        ChargeRepository $chargeRepository
    ) {
        $this->userRepository = $userRepository;
        $this->cardRepository = $cardRepository;
        $this->chargeRepository = $chargeRepository;

        // Set Stripe API key from configuration
        Stripe::setApiKey(Configurations::getByKey('stripe_secret_key'));
    }

    /**
     * Create a Stripe customer for the user.
     *
     * @param \App\Models\User $user
     * @return string Customer ID
     */
    public function createCustomer($user)
    {
        try {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);

            $this->userRepository->updateStripeCustomerId($user, $customer->id);

            return $customer->id;
        } catch (Exception $e) {
            throw new Exception('Failed to create Stripe customer: ' . $e->getMessage());
        }
    }

    /**
     * Create a payment method in Stripe.
     *
     * @param array $cardData
     * @return \Stripe\PaymentMethod
     */
    public function createPaymentMethod($cardData)
    {
        try {
            return PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => $cardData['number'],
                    'exp_month' => $cardData['exp_month'],
                    'exp_year' => $cardData['exp_year'],
                    'cvc' => $cardData['cvc'],
                ],
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create payment method: ' . $e->getMessage());
        }
    }

    /**
     * Attach payment method to customer.
     *
     * @param string $paymentMethodId
     * @param string $customerId
     * @return \Stripe\PaymentMethod
     */
    public function attachPaymentMethod($paymentMethodId, $customerId)
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customerId]);
            return $paymentMethod;
        } catch (Exception $e) {
            throw new Exception('Failed to attach payment method: ' . $e->getMessage());
        }
    }

    /**
     * Register a card for a user.
     *
     * @param \App\Models\User $user
     * @param array $cardData
     * @return array
     */
    public function registerCard($user, $cardData)
    {
        try {
            // Check if user has a Stripe customer ID, if not create one
            $customerId = $user->stripe_customer_id;
            if (!$customerId) {
                $customerId = $this->createCustomer($user);
            }

            // Create payment method
            $paymentMethod = $this->createPaymentMethod($cardData);

            // Attach payment method to customer
            $this->attachPaymentMethod($paymentMethod->id, $customerId);

            // Store card in our database
            $card = $this->cardRepository->create([
                'user_id' => $user->id,
                'stripe_payment_method_id' => $paymentMethod->id,
                'last_four' => substr($cardData['number'], -4),
                'brand' => $paymentMethod->card->brand,
                'exp_month' => $paymentMethod->card->exp_month,
                'exp_year' => $paymentMethod->card->exp_year,
            ]);

            return [
                'card' => $card,
                'stripe_response' => $paymentMethod->toArray()
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to register card: ' . $e->getMessage());
        }
    }

    /**
     * Charge a card.
     *
     * @param \App\Models\User $user
     * @param \App\Models\UserCard $card
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @return array
     */
    public function chargeCard($user, $card, $amount, $currency = 'usd', $description = null)
    {
        try {
            // Amount in cents
            $amountInCents = (int)($amount * 100);

            // Create charge in Stripe
            $charge = Charge::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'customer' => $user->stripe_customer_id,
                'source' => $card->stripe_payment_method_id,
                'description' => $description ?? "Charge for {$user->email}",
            ]);

            // Store charge in our database
            $chargeRecord = $this->chargeRepository->create([
                'user_id' => $user->id,
                'user_card_id' => $card->id,
                'stripe_charge_id' => $charge->id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $charge->status,
                'description' => $description,
            ]);

            return [
                'charge' => $chargeRecord,
                'stripe_response' => $charge->toArray()
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to charge card: ' . $e->getMessage());
        }
    }
}
