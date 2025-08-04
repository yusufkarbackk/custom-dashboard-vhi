<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\AdminService;
use App\Services\AdminToken;
use App\Services\SecurityService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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

    protected $adminService;
    protected $securityService;

    public function __construct(AdminService $adminService, SecurityService $securityService)
    {
        $this->$adminService = $adminService;
        $this->$securityService = $securityService;
    }

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
                $authToken = $this->adminService->getAdminToken();
                //dd($auth->header('X-Subject-Token'));

                $domainId = $this->adminService->createDomain('domain-user-' . $user->name, $authToken);
                if (empty($domainId)) {
                    Log::error("Failed to create domain");

                    throw ValidationException::withMessages([
                        'create_domain' => 'An internal error occurred while creating domain. Please try again.',
                    ]);
                }

                $projectId = $this->adminService->createProject('project-user-' . $user->name, $domainId, $authToken);

                $vhiUserId = $this->adminService->createUser(
                    $user->name,
                    $domainId,
                    $input['password'],
                    $authToken
                );

                $assignDomainAdminRole = $this->adminService->assignDomainAdminRole($authToken, $vhiUserId, $projectId);
                if (!$assignDomainAdminRole) {
                    Log::error("Failed to assign domain admin role to user {$vhiUserId} in project {$projectId}");

                    throw ValidationException::withMessages([
                        'assign_domain_role' => 'An internal error occurred while assigning domain admin role to the user. Please try again.',
                    ]);
                }

                $this->adminService->attachPubNAT204Network($authToken, $projectId);
                $user->update([
                    'vhi_user_id' => $vhiUserId,
                    'vhi_domain_id' => $domainId
                ]);

                $user->projects()->create([
                    'name' => 'user-' . $user->name,
                    'vhi_project_id' => $projectId,
                ]);

                Log::info(message: "User: " . $user);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                Log::error("VHI Sync Failed: " . $th->getMessage() . $th->getLine() . $th->getFile());
            }

            return $user;
        });
    }
}
