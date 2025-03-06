<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class AuthenticateWithToken
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authorization token required'
            ], 401);
        }

        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'INVALID TOKEN'
            ], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
