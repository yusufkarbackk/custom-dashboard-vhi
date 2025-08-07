<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminService
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

    public function assignRole()
    {
        Http::withoutVerifying()
            ->post(getenv('BASE_URL') . "/v3/projects/5605ec29bff444ad85e8a0377b5d21c3/users/b0d66b14b1c24aee9e76335884fac4a5/roles/051220db37404048aedcc2e89e28fd12", [
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
    }

    public function createDomain($domainName, $authToken): string
    {
        $domain = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $authToken,
                'Content-Type' => 'application/json',
            ])
            ->post(getenv('BASE_URL') . "/v3/domains", [
                'domain' => [
                    'name' => $domainName,
                    'description' => "tes domain",
                    'enabled' => true,
                ],
            ])->throw();
        //dd($domain->json('domain.id'));
        return $domain->json('domain.id');
    }

    public function createProject($projectName, $domainId, $authToken): string
    {
        $project = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $authToken,
                'Content-Type' => 'application/json',
            ])
            ->post(getenv('BASE_URL') . "/v3/projects", [
                'project' => [
                    'name' => $projectName,
                    'description' => "Isolated project for {$projectName}",
                    'enabled' => true,
                    'domain_id' => $domainId,
                ],
            ])->throw();
        return $project->json('project.id');
    }

    public function createUser($userName, $domainId, $password, $authToken): string
    {
        $user = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $authToken,
                'Content-Type' => 'application/json',
            ])
            ->post(getenv('BASE_URL') . "/v3/users", [
                'user' => [
                    'name' => $userName,
                    'domain_id' => $domainId,
                    'password' => $password,
                    'enabled' => true,
                ],
            ])->throw();
        return $user->json('user.id');
    }

    public function assignDomainAdminRole($authToken, $userId, $projectId): bool
    {
        try {
            Http::withoutVerifying()
                ->withHeaders([
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                ])
                ->put(getenv('BASE_URL') . "/v3/projects/$projectId/users/$userId/roles/" . env('DOMAIN_ADMIN_ID'));
            return true;
        } catch (\Throwable $th) {
            Log::error("error assign domain admin role: " . $th->getMessage());
            return false;
        }
    }

    public function updateUserPassword(string $vhiUserId, string $newPassword): bool
    {
        $this->refreshAdminToken();
        $admin_token = Session::get('vhi_admin_token');
        Log::info("Admin token: " . $admin_token);

        $endpoint = env('BASE_URL') . "/v3/users/{$vhiUserId}";

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-Auth-Token' => $admin_token,
                'Content-Type' => 'application/json',
            ])->patch($endpoint, [
                'user' => [
                    'password' => $newPassword,
                ],
            ]);

            if ($response->successful()) {
                Log::info("Successfully updated VHI password for user {$vhiUserId}.");
                return true;
            }

            Log::error("Failed to update VHI password for user {$vhiUserId}.", [
                'response' => $response->json()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('VHI API Connection Error during password update.', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function attachPubNAT204Network($authToken, $projectId)
    {
        try {
            Http::withoutVerifying()
                ->withHeaders([
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                ])
                ->post(getenv('NEUTRON_URL') . "/v2.0/rbac-policies", [
                    'rbac_policy' => [
                        "object_type" => "network",
                        "object_id" => env('PUBVNAT_ID'),
                        "action" => "access_as_shared",
                        "target_tenant" => $projectId
                    ],
                ]);
            return true;
        } catch (\Throwable $th) {
            Log::error("error assign domain admin role: " . $th->getMessage());
            return false;
        }
    }
}
