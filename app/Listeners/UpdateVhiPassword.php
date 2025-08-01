<?php

namespace App\Listeners;

use App\Services\AdminToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\PasswordReset;
use Log;
use App\Models\User;

class UpdateVhiPassword
{
    protected AdminToken $admin_token;

    /**
     * Create the event listener.
     */
    public function __construct(AdminToken $admin_token)
    {
        $this->admin_token = $admin_token;
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        // Ensure we are working with the expected User model.
        if (!($event->user instanceof User)) {
            Log::warning('PasswordReset event fired for a non-User model.', ['user_type' => get_class($event->user)]);
            return;
        }

        $user = $event->user;

        // You must have a way to link your Laravel user to their VHI user ID.
        // This example assumes you have a 'vhi_user_id' column on your users table.
        if (empty($user->vhi_user_id)) {
            Log::warning("User {$user->email} does not have a VHI user ID. Skipping password sync.");
            return;
        }

        // The new password is not directly available in the event.
        // We get it from the request input that triggered the reset.
        $newPassword = request()->input('password');

        if (empty($newPassword)) {
            Log::error("Could not retrieve new password from request for user {$user->email}.");
            return;
        }

        Log::info("Password reset event caught for user {$user->email}. Attempting to sync with VHI.");

        // Call the service to update the password in VHI
        $this->admin_token->updateUserPassword($user->vhi_user_id, $newPassword);
    }
}
