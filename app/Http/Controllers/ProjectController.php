<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $domain_id = session('domain_id');
        // $response = Http::withHeaders([
        //     'X-Auth-Token' => session('vhi_token'), // or your token variable
        // ])
        //     ->withoutVerifying() // bypass SSL cert validation
        //     ->get("https://10.21.0.240:5000/v3/projects?domain_id={$domain_id}");

        // $data = json_decode($response->getBody(), true);
        //dd($domain_id);
        //dd($data);
        //dd($data['domains']);
        $data = Auth::user()->projects()->get();
        //dd($data);
        return view('project.index', ['projects' => $data]);
    }
}
