<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AdminToken
{
    public function getAdminToken()
    {
        $auth = Http::withoutVerifying()
            ->post(getenv('BASE_URL') . "/v3/auth/tokens", [
                'auth' => [
                    'identity' => [
                        'methods' => ['password'],
                        'password' => [
                            'user' => [
                                'name' => getenv('VHI_ADMIN_USERNAME'),
                                'domain' => ['name' => getenv('VHI_ADMIN_DOMAIN')],
                                'password' => getenv('VHI_ADMIN_PASSWORD'),
                            ],
                        ],
                    ],
                    'scope' => [
                        'project' => [
                            'id' => getenv('VHI_ADMIN_PROJECT_ID'),
                        ],
                    ],
                ],
                'authUrl' => getenv('BASE_URL') . '/v3/auth/tokens',
            ]);

        return $auth->header('X-Subject-Token');
    }

    public function refreshAdminToken()
    {
        $auth = Http::withoutVerifying()
            ->post(getenv('BASE_URL') . "/v3/auth/tokens", [
                'auth' => [
                    'identity' => [
                        'methods' => ['password'],
                        'password' => [
                            'user' => [
                                'name' => getenv('VHI_ADMIN_USERNAME'),
                                'domain' => ['name' => getenv('VHI_ADMIN_DOMAIN')],
                                'password' => getenv('VHI_ADMIN_PASSWORD'),
                            ],
                        ],
                    ],
                    'scope' => [
                        'project' => [
                            'id' => getenv('VHI_ADMIN_PROJECT_ID'),
                        ],
                    ],
                ],
                'authUrl' => getenv('BASE_URL') . '/v3/auth/tokens',
            ]);

        $token = $auth->header('X-Subject-Token');
        Session::put('vhi_admin_token', $token);
    }
}
