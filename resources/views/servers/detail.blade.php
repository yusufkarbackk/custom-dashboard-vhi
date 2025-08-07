@extends('layouts.app')
@section('title', 'Project Details')

@section('content')
<div class="container">
    <h1>{{ $data['name'] }}</h1>
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
            <h2>Server Information</h2>
            <p><strong>Status:</strong> {{ $data['status']}}</p>
            <p><strong>Memory MB:</strong> {{ $data['memory_mb']}}</p>
            <p><strong>VCPUs:</strong> {{ $data['vcpus']}}</p>
            <p><strong>Address:</strong> {{ $data['addresses']['pubNAT204'][0]['addr']}}</p>
            <p><strong>Operating System:</strong> {{ $data['image_metadata']['os_distro']}}</p>
            <p><strong>Created At:</strong> {{ $data['created'] }}</p>
        </div>
        <form action="{{route('servers.delete', ['projectId' => $projectId, 'serverId' => $data['id']])}}" method="post"
            onsubmit="return confirm('Are you sure you want to delete this VM?');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger">
                Delete
            </button>
        </form>
    </div>
    </table>
</div>

<!-- Add more sections as needed -->
@endsection