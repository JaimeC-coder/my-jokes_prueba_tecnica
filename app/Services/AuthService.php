<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->userRepository->create($data);
    }

    /**
     * Login a user.
     *
     * @param string $email
     * @param string $password
     * @return \App\Models\User
     * @throws \Exception
     */
    public function login($email, $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('BAD CREDENTIALS');
        }

        return $user;
    }

    /**
     * Validate user token.
     *
     * @param string $token
     * @return \App\Models\User
     * @throws \Exception
     */
    public function validateToken($token)
    {
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            throw new \Exception('INVALID TOKEN');
        }

        return $user;
    }
}
