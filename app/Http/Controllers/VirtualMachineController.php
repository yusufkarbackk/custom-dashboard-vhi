<?php

namespace App\Http\Controllers;

use App\Services\VirtualMachineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VirtualMachineController extends Controller
{
    private VirtualMachineService $virtualMachineService;

    public function __construct(VirtualMachineService $virtualMachineService)
    {
        $this->virtualMachineService = $virtualMachineService;
    }
    public function index()
    {
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
        $request->validate([
            'name' => 'required',
            'imageRef' => 'required',
            'flavorRef' => 'required',
            'networks' => 'required|array',
        ]);

        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => session('vhi_admin_token'), // or your token variable
            ])
                ->withoutVerifying() // bypass SSL cert validation
                ->post('https://10.21.0.240:8774/v2.1/servers', [
                    'server' => [
                        'name' => $request->name,
                        // 'imageRef' => $request->imageRef,
                        'flavorRef' => $request->flavorRef,
                        'networks' => $request->networks,
                    ],
                ]);

            dd($response->json());

            return redirect()->route('servers.index')->with('success', 'Server created successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('servers.index')->with('error', 'Failed to create server: ' . $th->getMessage());
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
}
