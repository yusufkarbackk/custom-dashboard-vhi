@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Projects</h1>
            <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <table id="providerTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Enabled</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project['id'] }}</td>
                            <td>{{ $project['name'] }}</td>
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
            $('#providerTable').DataTable();
        });
    </script>
@endpush