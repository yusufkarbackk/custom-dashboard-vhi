<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VirtualMachineService
{
    private $admin_token;
    public function __construct()
    {
        $this->admin_token = new AdminToken();
    }
    public function getImages()
    {
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }
        $response = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:9292/v2/images');

        return $response->json();
    }

    public function getFlavors()
    {
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }

        $response = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors');

        $tiny = array_filter($response->json('flavors'), fn($f) => $f['name'] === 'tiny');
        $small = array_filter($response->json('flavors'), fn($f) => $f['name'] === 'small');
        $medium = array_filter($response->json('flavors'), fn($f) => $f['name'] === 'medium');
        $large = array_filter($response->json('flavors'), fn($f) => $f['name'] === 'large');
        $extraLarge = array_filter($response->json('flavors'), fn($f) => $f['name'] === 'xlarge');

        // dd($tiny);

        $detailedTiny = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors/' . $tiny[1]['id'])
            ->json('flavor');
        $detailedSmall = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors/' . $small[2]['id'])
            ->json('flavor');

        $detailedMedium = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors/' . $medium[3]['id'])
            ->json('flavor');
        $detailedLarge = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors/' . $large[4]['id'])
            ->json('flavor');
        $detailedExtraLarge = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/flavors/' . $extraLarge[5]['id'])
            ->json('flavor');

        return [
            'tiny' => $detailedTiny,
            'small' => $detailedSmall,
            'medium' => $detailedMedium,
            'large' => $detailedLarge,
            'xlarge' => $detailedExtraLarge,
        ];
    }

    public function getNetworks()
    {
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }
        $response = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:9696/v2.0/networks');
        return $response->json();
    }

    public function attachNetwork($project_id)
    {
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }

        $response = Http::withHeaders([
            'X-Auth-Token' => $admin_token,
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->post(env('NEUTRON_URL') . "/v2.0/rbac-policies", [
                'rbac_policy' => [
                    'object_type' => 'network',
                    'object_id' => env('PUBVNAT_ID'),
                    'action' => 'access_as_shared',
                    'target_tenant' => $project_id,
                ],
            ]);

        return $response->json();
    }
}