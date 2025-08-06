@extends('layouts.app')
@section('title', 'Project Details')

@section('content')
<div class="container">
    <h1>{{ $project['name'] }}</h1>
    <p>{{ $project['description'] }}</p>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @elseif (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Project Details -->
    <div class="card mb-5">
        <div class="card-body">
            <h2>Project Information</h2>
            <p><strong>ID:</strong> {{ $project['id'] }}</p>
            <p><strong>Name:</strong> {{ $project['name'] }}</p>
            <p><strong>Description:</strong> {{ $project['description'] }}</p>
            <p><strong>Enabled:</strong> {{ $project['enabled'] ? 'Yes' : 'No' }}</p>
            <p><strong>Created At:</strong> {{ $project['created_at'] }}</p>
        </div>
    </div>

    <a href="{{ route('servers.create', $project['id']) }}">
        <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
            <i class="fas fa-plus"></i> Create Instance
        </button>
    </a>
    <table id="projectTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (count($servers) > 0)
            @foreach ($servers as $server)
            <tr>
                <td>{{ $server['id'] }}</td>
                <td>
                    <a href="{{route('servers.show', ['projectId' => $project['id'], 'serverId' => $server['id']])}}">
                        {{ $server['name'] }}
                    </a>
                </td>
                <td>
                    <form action="{{route('servers.delete', ['projectId' => $project['id'], 'serverId' => $server['id']])}}" method="post"
                        onsubmit="return confirm('Are you sure you want to delete this VM?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="2">No servers found for this project.</td>
            </tr>

            @endif
        </tbody>
    </table>
</div>

<!-- Add more sections as needed -->
@endsection