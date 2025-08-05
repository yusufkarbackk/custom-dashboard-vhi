<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('name', $request->name)->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {
                //dd($user->projects()->first()->vhi_project_id);
                //dd($request->password);
                try {
                    //($user->name, $request->password, $user->vhi_domain_id);
                    $response = Http::withoutVerifying()
                        ->post(getenv('BASE_URL') . '/v3/auth/tokens', [
                            'auth' => [
                                'identity' => [
                                    'methods' => ['password'],
                                    'password' => [
                                        'user' => [
                                            'name' => $user->name,
                                            'domain' => ['id' => $user->vhi_domain_id],
                                            'password' => $request->password,
                                        ],
                                    ],
                                ],
                                'scope' => [
                                    'project' => [
                                        'name' => $user->projects()->first()->name,
                                        'domain' => ['id' => $user->vhi_domain_id],
                                    ],
                                ],
                            ],
                            'authUrl' => getenv('BASE_URL') . '/v3/auth/tokens',
                        ]);
                    Session::put("vhi_user_token", $response->header('X-Subject-Token'));
                    //dd($user->projects->first()->name);
                    //dd($response->headers());
                    if ($response->successful()) {
                        $token = $response->header('X-Subject-Token');
                        session(['vhi_token' => $token, 'domain_id' => $user->vhi_domain_id]);
                        Log::info('VHI login successful', ['token' => $token]);
                        return $user;
                    } else {
                        Log::error('VHI login failed', ['response' => $response->body()]);
                    }
                } catch (\Exception $e) {
                    Log::error('VHI request error', ['exception' => $e->getMessage() . $e->getFile() . $e->getLine()]);
                }
            }
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
