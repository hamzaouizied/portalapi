@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <form class="p-5" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" placeholder="Enter email">
                    @error('email')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                    @if (isset($error))
                    <div class="alert alert-danger mt-1 mb-1">{{ $error }}</div>
                    @endif
                    @if (isset($success))
                    <div class="alert alert-success mt-1 mb-1">{{ $success }}</div>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label for="ReCaptcha">Recaptcha:</label>
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </form>

        </div>
    </div>
@endsection
