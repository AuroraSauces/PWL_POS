@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Edit Level</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('level/'.$level->level_id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Level</label>
                <input type="text" class="form-control" name="level_nama" value="{{ $level->level_nama }}" required>
                @error('level_nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ url('level') }}" class="btn btn-default">Kembali</a>
        </form>
    </div>
</div>
@endsection
