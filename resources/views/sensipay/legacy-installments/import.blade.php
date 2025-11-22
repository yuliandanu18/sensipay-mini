@extends('sensipay.layout')

@section('content')
    <h1>Import Cicilan Legacy</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form action="{{ route('sensipay.legacy-installments.import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">File cicilan (xlsx / csv)</label>
            <input type="file" name="file" id="file" required>
            @error('file')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Import</button>
    </form>
@endsection
