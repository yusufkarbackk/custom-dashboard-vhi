<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function domains()
    {
        //dd(session('vhi_token'));
        // Hit the VHI API
        $response = Http::withHeaders([
            'X-Auth-Token' => session('vhi_token'), // or your token variable
        ])
            ->withoutVerifying() // bypass SSL cert validation
            ->get('https://10.21.0.240:5000/v3/domains');

        $data = json_decode($response->getBody(), true);
        dd($data);
        //dd($data['domains']);
        return view('domain.index', ['domains' => $data['domains']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'enabled' => 'nullable|in:true,false',
            'options' => 'nullable|string', // JSON text
        ]);

        $payload = [
            'domain' => [
                'name' => $validated['name'],                                                                                                                                                                                                   
                'description' => $validated['description'] ?? '',
                'enabled' => $validated['enabled'] ?? 'true',
                'options' => $validated['options'] ? json_decode($validated['options'], true) : new \stdClass(),
            ]
        ];

        try {
            $response = Http::withToken(session('vhi_token'))
                ->withoutVerifying() // Skip SSL verification if needed
                ->post('https://10.21.0.240:5000/v3/domains', $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Domain added successfully!');
            }

            return redirect()->back()->withErrors(['msg' => 'API error: ' . $response->body()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Exception: ' . $e->getMessage()]);
        }
    }
}
