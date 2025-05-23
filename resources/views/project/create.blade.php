@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Project Name</label>
                <input type="text" name="name" id="name" class="form-control" required>

                <label for="description">Project Description</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Project</button>
        </form>
    </div>
@endsection