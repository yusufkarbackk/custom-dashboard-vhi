<?php

namespace App\Http\Controllers;

use App\services\UserService;
use App\Services\VirtualMachineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

use function Laravel\Prompts\progress;

class VirtualMachineController extends Controller
{
    protected $userService;
    protected $virtualMachineService;

    public function __construct(UserService $userService, VirtualMachineService $virtualMachineService)
    {
        $this->userService = $userService;
        $this->virtualMachineService = $virtualMachineService;
    }
    public function index()
    {
        $user = Auth::user();

        //dd($user);
        $user_token = Session::get('vhi_admin_token');
        if (!$user_token) {
            $this->userService->refreshUserToken($user->name, $user->vhi_domain_id, $user->password, $user->vhi_project_id->first());
            $user_token = Session::get('vhi_user_token');
        }
        $response = Http::withHeaders([
            'X-Auth-Token' => session('vhi_token'), // or your token variable
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/servers');


        $data = json_decode($response, true);
        //  dd($data);
        return view('servers.index', ['servers' => $data['servers']]);
    }

    public function create()
    {
        $images = $this->virtualMachineService->getImages();
        $flavors = $this->virtualMachineService->getFlavors();
        //d($flavors);
        $networks = $this->virtualMachineService->getNetworks();
        // dd($networks['networks']);
        // dd($images['images']);


        return view('servers.create', ['images' => $images['images'], 'flavors' => $flavors, 'networks' => $networks['networks']]);
    }

    public function store(Request $request)
    {
        //dd($request);
        $request->validate([
            'vm_name' => 'required',
            'flavor_id' => 'required',
            'image_select' => 'required',
        ]);

        $user_token = Session::get('vhi_user_token');
        //dd($user_token);
        $portId = $this->virtualMachineService->createPort("port-" . $request->vm_name);
        //dd($portId);

        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $user_token, // or your token variable
            ])
                ->withoutVerifying() // bypass SSL cert validation
                ->post(env('NOVA_URL') . '/servers', [
                    'server' => [
                        'name' => $request->vm_name,
                        // 'imageRef' => $request->imageRef,
                        'flavorRef' => $request->flavor_id,
                        'networks' => [
                            [
                                "port" => $portId
                            ]
                        ],
                        "block_device_mapping_v2" => [
                            [
                                "boot_index" => "0",
                                "uuid" => $request->image_select,
                                "source_type" => 'image',
                                "volume_size" => 10,
                                "destination_type" => 'volume',
                                "delete_on_termination" => true
                            ]
                        ]
                    ],
                ]);

            //dd($response->json());

            return redirect()->route('projects.index')->with('success', 'Server created successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('servers.create')->with('error', 'Failed to create server: ' . $th->getMessage());
        }
    }

    public function selectFlavor(Request $request)
    {
        $request->validate([
            'flavor_id' => 'required|integer',
        ]);

        session(['chosen_flavor_id' => $request->flavor_id]);
        dd($request->flavor_id);

        // try {
        //     $flavor = $this->virtualMachineService->getFlavorById($flavorId);
        //     return response()->json($flavor);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Failed to retrieve flavor: ' . $e->getMessage()], 500);
        // }
    }

    public function show($projectId, $serverId)
    {
        $user_token = Session::get('vhi_user_token');
        //dd($user_token);
        //dd($user_token);
        // if (!$user_token) {
        //     $this->userService->refreshUserToken($user->name, $user->vhi_domain_id, $user->password, $id);
        //     $user_token = Session::get('vhi_user_token');
        // }

        $serverResponse = Http::withHeaders([
            'X-Auth-Token' => $user_token
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get(env('NOVA_URL') . $projectId . '/servers' . '/' . $serverId);
        //dd($serverResponse);
        //dd($serverResponse['server']);
        return view('servers.detail', ['data' => $serverResponse['server'], 'projectId' =>$projectId]);
    }

    public function delete($projectId, $serverId)
    {
        $user_token = Session::get('vhi_user_token');

        try {
            $serverResponse = Http::withHeaders([
                'X-Auth-Token' => $user_token
            ])
                ->withoutVerifying() // bypass SSL cert validation
                ->delete(env('NOVA_URL') . '/' . $projectId . '/servers' . '/' . $serverId);
            //dd($serverResponse);

            return redirect()->route('projects.show', $projectId)->with('success', 'VM deleted successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('projetcs.show', $projectId)->with('error', 'Failed to delete VM: ' . $th->getMessage());
        }
    }
}
