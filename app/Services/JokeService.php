<?php
// app/Services/JokeService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class JokeService
{
    protected $apiBaseUrl = 'https://sv443.net/jokeapi/v2';

    /**
     * Get a random joke.
     *
     * @return array
     */
    public function getRandomJoke()
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/joke/Any");

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch joke from API');
        } catch (\Exception $e) {
            throw new \Exception('Joke service error: ' . $e->getMessage());
        }
    }
}
