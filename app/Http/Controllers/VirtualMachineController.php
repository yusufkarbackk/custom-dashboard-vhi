<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VirtualMachineController extends Controller
{
    public function index()
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => session('vhi_token'), // or your token variable
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:5000/servers'); 

        $data = json_decode($response->getBody(), true);
        //dd($data['domains']);
        return view('servers.index', ['servers' => $data['servers']]);
    }
}
