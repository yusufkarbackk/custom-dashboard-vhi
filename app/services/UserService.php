<?php

namespace App\services;

use Dotenv\Util\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class UserService
{
    public function getUserToken(string $username, string $domain, string $password, string $projectId)
    {
        $auth = Http::withoutVerifying()
            ->post(getenv('BASE_URL') . "/v3/auth/tokens", [
                'auth' => [
                    'identity' => [
                        'methods' => ['password'],
                        'password' => [
                            'user' => [
                                'name' => $username,
                                'domain' => ['name' => $domain],
                                'password' => $password,
                            ],
                        ],
                    ],
                    'scope' => [
                        'project' => [
                            'id' => $projectId,
                        ],
                    ],
                ],
                'authUrl' => getenv('BASE_URL') . '/v3/auth/tokens',
            ]);

        return $auth->header('X-Subject-Token');
    }

    public function refreshUserToken(string $username, string $domain, string $password, string $projectId)
    {
        $auth = Http::withoutVerifying()
            ->post(getenv('BASE_URL') . "/v3/auth/tokens", [
                'auth' => [
                    'identity' => [
                        'methods' => ['password'],
                        'password' => [
                            'user' => [
                                'name' => $username,
                                'domain' => ['name' => $domain],
                                'password' => $password,
                            ],
                        ],
                    ],
                    'scope' => [
                        'project' => [
                            'id' => $projectId,
                        ],
                    ],
                ],
                'authUrl' => getenv('BASE_URL') . '/v3/auth/tokens',
            ]);

        $token = $auth->header('X-Subject-Token');
        //dd($token);
        Session::put('vhi_user_token', $token);
    }
}
