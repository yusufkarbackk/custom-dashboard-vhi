<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\AdminToken;
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
     * Validate and create a newly  ed user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            //1. Create the user locally
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            try {
                $authToken = (new AdminToken())->getAdminToken();
                //dd($auth->header('X-Subject-Token'));

                $domainId = (new AdminToken())->createDomain('domain-user-' . $user->name, $authToken);

                $projectId = (new AdminToken())->createProject('project-user-' . $user->name, $domainId, $authToken);

                $vhiUserId = (new AdminToken())->createUser(
                    $user->name,
                    $domainId,
                    $input['password'],
                    $authToken
                );

                (new AdminToken())->assignDomainAdminRole($authToken, $vhiUserId, $projectId);

                $user->update([
                    'vhi_user_id' => $vhiUserId,
                ]);

                $user->projects()->create([
                    'name' => 'user-' . $user->name,
                    'vhi_domain_id' => $domainId,
                    'vhi_project_id' => $projectId,
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
