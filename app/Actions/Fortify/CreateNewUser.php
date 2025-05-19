<?php

namespace App\Actions\Fortify;

use App\Models\User;
use DB;
use Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Str;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            //1. Create the user locally
            $user = User::create([
                'name' => $input['name'],
                //'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            try {
                $auth = Http::withoutVerifying()
                    ->post('https://10.21.0.240:5000/v3/auth/tokens', [
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
                        'authUrl' => 'https://10.21.0.240:5000/v3/auth/tokens',
                    ]);
                //dd($auth->header('X-Subject-Token'));
                $authToken = $auth->header('X-Subject-Token');

                $domainResp = Http::withoutVerifying()->withHeaders([
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                ])->post(getenv('BASE_URL') . "/v3/domains", [
                            'domain' => [
                                'name' => 'domain-user-' . $user->name,
                                'description' => "Isolated domain for {$user->username}",
                                'enabled' => true,
                            ],
                        ])->throw();

                $domainId = $domainResp->json('domain.id');

                $projectResp = Http::withoutVerifying()->withHeaders([
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                ])->post(getenv('BASE_URL') . "/v3/projects", [
                    'project' => [
                        'name' => 'project-user-' . $user->name,
                        'description' => "Isolated project for {$user->name}",
                        'enabled' => true,
                        'domain_id' => $domainId
                    ],
                ]);
                $projectId = $projectResp->json('project.id');


                $userResp = Http::withoutVerifying()->withHeaders([
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                ])->post('https://10.21.0.240:5000/v3/users', [
                    'user' => [
                        'name' => $input['name'],
                        'domain_id' => $domainId,
                        'password' => $input['password'],
                        'enabled' => true,
                    ],
                ])->throw();

                $vhiUserId = $userResp->json('user.id');

                $user->update([
                    'vhi_user_id' => $vhiUserId,
                    'vhi_domain_id' => $domainId,
                ]);

                $user->projects()->create([
                    'name' => 'user-' . $user->name,
                    'vhi_user_id' => $vhiUserId,
                    'vhi_domain_id' => $domainId,
                    'vhi_project_id' => $projectResp->json('project.id'),
                ]);
                
                \Log::info(message: "User: " . $user);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                \Log::error("VHI Sync Failed: " . $th->getMessage() . $th->getLine() . $th->getFile());

            }

            return $user;
        });
    }
}
