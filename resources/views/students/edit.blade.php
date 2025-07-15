@extends('layouts.app')

@section('title', 'Edit Student - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Student</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Students
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('students.update', $student->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="username" class="form-label">Username *</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $student->username) }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name *</label>
                <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required>
                @error('full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="school" class="form-label">School</label>
                <input type="text" class="form-control @error('school') is-invalid @enderror" id="school" name="school" value="{{ old('school', $student->school) }}">
                @error('school')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="class" class="form-label">Class</label>
                <input type="text" class="form-control @error('class') is-invalid @enderror" id="class" name="class" value="{{ old('class', $student->class) }}">
                @error('class')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="class_type" class="form-label">Class Type</label>
                <input type="text" class="form-control @error('class_type') is-invalid @enderror" id="class_type" name="class_type" value="{{ old('class_type', $student->class_type) }}">
                @error('class_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 