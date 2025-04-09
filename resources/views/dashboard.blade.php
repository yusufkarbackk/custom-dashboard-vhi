@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Dashboard</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <table id="example" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>CPU</th>
                        <th>RAM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>VM-01</td>
                        <td>Running</td>
                        <td>4 vCPU</td>
                        <td>8 GB</td>
                    </tr>
                    <tr>
                        <td>VM-02</td>
                        <td>Stopped</td>
                        <td>2 vCPU</td>
                        <td>4 GB</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>
@endpush