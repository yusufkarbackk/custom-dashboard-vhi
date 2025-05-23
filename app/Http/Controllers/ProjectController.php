<?php

namespace App\Http\Controllers;

use App\Services\AdminToken;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Log;

class ProjectController extends Controller
{
    private $admin_token;
    public function __construct()
    {
        $this->admin_token = new AdminToken();
    }
    public function index()
    {
        // $data = Auth::user()->projects()->get();
        // //dd($data);
        $admin_token = Session::get('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = Session::get('vhi_admin_token');
        }
        $domain_id = Auth::user()->vhi_domain_id;
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
        $admin_token = session('vhi_admin_token');
        if (!$admin_token) {
            $this->admin_token->refreshAdminToken();
            $admin_token = session('vhi_admin_token');
        }
        $data = Http::withoutVerifying()
            ->withHeaders([
                'X-Auth-Token' => $admin_token,
            ])
            ->get(getenv('BASE_URL') . '/v3/projects/' . $id)->json();
        //dd($data);

        $serverResponse = Http::withHeaders([
            'X-Auth-Token' => session('vhi_token'), // or your token variable
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:8774/v2.1/servers');


        $servers = json_decode($serverResponse, true);
        return view('project.detail', ['project' => $data['project'], 'servers' => $servers['servers']]);
    }
}
