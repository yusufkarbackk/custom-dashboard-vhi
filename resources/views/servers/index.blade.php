@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Servers</h1>
            <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                <i class="fas fa-plus"></i> Add Server
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servers as $server)
                        <tr>
                            <td>{{ $server['id'] }}</td>
                            <td>{{ $server['name'] }}</td>
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