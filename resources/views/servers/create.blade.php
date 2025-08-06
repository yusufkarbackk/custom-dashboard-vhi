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
        <h1 class="m-0">Create New Virtual Machine</h1>
        <a href="{{ route('projects.index') }}">
            <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                <i class="fas fa-back"></i> Back to Projects
            </button>
        </a>
    </div>
</div>

<!-- Create VM Form -->
<div class="container py-4">
    <h1>Create New VM</h1>

    <form action="{{route('servers.store')}}" method="post">
        @csrf
        <input type="hidden" name="flavor_id" value="{{ session('selected_flavor') }}">

        {{-- VM Name --}}
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="vm_name" class="form-control" required>
        </div>

        {{-- Flavor --}}
        <div class="mb-3">
            <label class="form-label h3 flavor">Flavor</label>
            @livewire('flavor-selector', ['flavors' => $flavors])

            {{-- Image --}}
            <div class="mb-3">
                <label class="form-label">Image</label>
                <select name="image_select" id="image_select" class="form-select" required>
                    @foreach($images as $img)
                    <option value="{{ $img['id'] }}">{{ $img['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-server me-2"></i>Create VM
            </button>
        </div>
    </form>
</div>
@endsection