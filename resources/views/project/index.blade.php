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
            <h1 class="m-0">Projects</h1>
            <a href="{{ route('projects.create') }}">
                <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                    <i class="fas fa-plus"></i> Create Project
                </button>
            </a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <table id="projectTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description`</th>
                        <th>Enabled</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project['id'] }}</td>
                            <td>
                                <a href="{{ route('projects.show', $project['id']) }}">
                                    {{ $project['name'] }}
                                </a>
                            </td>
                            <td>{{ $project['description'] }}</td>
                            <td>{{ $project['enabled'] }}</td>
                            <td>{{ $project['created_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#projectTable').DataTable();
        });
    </script>
@endpush