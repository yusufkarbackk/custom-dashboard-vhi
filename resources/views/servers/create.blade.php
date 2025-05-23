@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Create New Virtual Machine
                <a href="{{ route('servers.index') }}">
                    <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                        <i class="fas fa-plus"></i> Back to Servers
                    </button>
                </a>
        </div>
    </div>

    <!-- Create VM Form -->
    <div class="container py-4">
        <h1>Create New VM (Advanced)</h1>

        <form id="create-vm-form" method="POST" action="{{ route('servers.store') }}">
            @csrf

            {{-- VM Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Flavor --}}
            <div class="mb-3">
                <label class="form-label h3 flavor">Flavor</label>
                <div class="row g-4">
                    @foreach($flavors as $f => $flavor)
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="card">
                                <h5 class="card-header">{{ $flavor['name'] }}</h5>

                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>vCPU:</strong> {{ $flavor['vcpus'] }}<br>
                                        <strong>RAM:</strong> {{ floatval($flavor['ram']) / 1024 }} GB<br>
                                    </p>
                                    <a href="#" class="btn btn-primary">Select</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Image --}}
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <select name="image" class="form-select" required>
                        @foreach($images as $img)
                            <option value="{{ $img['id'] }}">{{ $img['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Networks --}}
                <div class="mb-3">
                    <label class="form-label">Networks</label>
                    <div class="row gy-2">
                        {{-- NIC #1 (fixed IP) --}}
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>NIC 1 (fixed IP)</h6>
                                <select name="networks[0][uuid]" class="form-select mb-2" required>
                                    @foreach($networks as $n)
                                        <option value="{{ $n['id'] }}">{{ $n['name'] }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="networks[0][fixed_ips][0][ip_address]" class="form-control"
                                    placeholder="192.168.128.10" required>
                            </div>
                        </div>

                        {{-- NIC #2 (automatic IP) --}}
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>NIC 2 (auto IP)</h6>
                                <select name="networks[1][uuid]" class="form-select" required>
                                    @foreach($networks as $n)
                                        <option value="{{ $n['id'] }}">{{ $n['name'] }}</option>
                                    @endforeach
                                </select>
                                {{-- no fixed_ips field here so Nova will pick an IP from the subnet --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security Groups --}}


                {{-- Boot from Volume --}}
                <div class="mb-3">
                    <label class="form-label">Boot Volume</label>
                    <div class="input-group">
                        <input type="text" name="block_device[uuid]" class="form-control" placeholder="Image/Volume UUID">
                        <input type="number" name="block_device[volume_size]" class="form-control"
                            placeholder="Volume Size (GB)" min="1">
                        <span class="input-group-text">
                            <input type="checkbox" name="block_device[delete_on_termination]" checked>
                            Delete on Termination
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-server me-2"></i>Create VM
                </button>
            </div>
        </form>
    </div>
@endsection