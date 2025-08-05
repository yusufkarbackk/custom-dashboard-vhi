<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\Services\AdminToken;
use App\services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    protected $adminService;
    protected $userService;
    public function __construct()
    {
        $this->adminService = new AdminService();
        $this->userService = new UserService();
    }
    public function index()
    {
        // $data = Auth::user()->projects()->get();
        // //dd($data);
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->adminService->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }
        $domain_id = Auth::user()->vhi_domain_id;
        Log::info('Fetching projects for domain ID: ' . $domain_id);
        $data = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $admin_token,
            ])
            ->get(getenv('BASE_URL') . '/v3/projects?domain_id=' . $domain_id)->json();
        //dd($data);
        return view('project.index', ['projects' => $data['projects']]);
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $resp = Http::withoutVerifying()
                ->withHeaders([
                    'X-Auth-Token' => Session::get('vhi_admin_token'),
                ])
                ->post(getenv('BASE_URL') . '/v3/projects', [
                    'project' => [
                        'name' => $request->name,
                        'description' => $request->description,
                        'domain_id' => Auth::user()->vhi_domain_id,
                    ],
                ]);
            //dd($resp->json());
            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        } catch (\Throwable $th) {
            Log::error('Failed to create project: ' . $th->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to create project: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();

        //dd($user->id);

        //dd($id);
        $user_token = Session::get('vhi_user_token');
        //dd($user_token);
        if (!$user_token) {
            $this->userService->refreshUserToken($user->name, $user->vhi_domain_id, $user->password, $id);
            $user_token = Session::get('vhi_user_token');
        }
        //dd($user_token);


        $admin_token = session('vhi_admin_token');
        if (!$admin_token) {
            $this->adminService->refreshAdminToken();
            $admin_token = session('vhi_admin_token');
        }

        $data = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $user_token,
            ])
            ->get(getenv('BASE_URL') . '/v3/projects/' . $id)->json();
        //dd($data);

        $serverResponse = Http::withHeaders([
            'X-Auth-Token' => $user_token
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/' . $id . '/servers');


        $servers = json_decode($serverResponse, true);
        //dd($servers);
        return view('project.detail', ['project' => $data['project'], 'servers' => $servers['servers']]);
    }
}
