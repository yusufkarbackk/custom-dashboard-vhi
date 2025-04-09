<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\User;
use Hash;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Log;

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
                //dd($request->password);
                try {
                    $response = Http::withoutVerifying()
                        ->post('https://10.21.0.240:5000/v3/auth/tokens', [
                            'auth' => [
                                'identity' => [
                                    'methods' => ['password'],
                                    'password' => [
                                        'user' => [
                                            'name' => 'admin',
                                            'domain' => ['name' => 'Default'],
                                            'password' => $request->password,
                                        ],
                                    ],
                                ],
                                'scope' => [
                                    'project' => [
                                        'id' => '6d0d5af6b3c84c9db82fe761d8187ff2',
                                    ],
                                ],
                            ],
                            'authUrl' => 'https://10.21.0.240:5000/v3/auth/tokens',
                        ]);

                    if ($response->successful()) {
                        $token = $response->header('X-Subject-Token');
                        session(['vhi_token' => $token]);
                        Log::info('VHI login successful', ['token' => $token]);
                    } else {
                        Log::error('VHI login failed', ['response' => $response->body()]);
                    }
                } catch (\Exception $e) {
                    Log::error('VHI request error', ['exception' => $e->getMessage()]);
                }
                return $user;
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
