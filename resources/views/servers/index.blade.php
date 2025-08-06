@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Servers</h1>
        <a href="{{ route('servers.create') }}" class="btn btn-primary mb-3 mt-3">
            <i class="fas fa-plus"></i> Add Server
        </a>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <table id="providerTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servers as $server)
                <tr>
                    <td>{{ $server['id'] }}</td>
                    <td>{{ $server['name'] }}</td>
                    <td>
                        <a href="">
                            <button class="btn btn-danger">
                                Delete
                            </button>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#providerTable').DataTable();
    });
</script>
@endpush