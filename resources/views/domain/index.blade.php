@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Domains</h1>
            <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                <i class="fas fa-plus"></i> Add Domain
            </button>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <table id="domainTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($domains as $domain)
                        <tr>
                            <td>{{ $domain['id'] }}</td>
                            <td>{{ $domain['name'] }}</td>
                            <td>{{ $domain['description'] }}</td>
                            <td>{{ $domain['created_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('domain.partials.add-modal')

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#domainTable').DataTable();
        });
    </script>
@endpush