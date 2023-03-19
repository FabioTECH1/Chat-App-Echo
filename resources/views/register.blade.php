@extends('layout.layout')
@section('title', 'Register | Chat-Wink')
@section('content')
    <div class="wrapper">
        <section class="form signup">
            <header>Chat-Wink</header>
            <form action="{{ route('register-user') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($errors->any() || Session::has('exists'))
                    <div class="error-text" style="display: block">
                        {{ Session::get('exists') }}
                        {{ $errors->first('password') }} <br>
                        {{ $errors->first('image') }}
                    </div>
                @endif
                <div class="name-details">
                    <div class="field input">
                        <label>First Name</label>
                        <input type="text" name="fname" placeholder="First name" value="{{ old('fname') }}" required>
                    </div>
                    <div class="field input">
                        <label>Last Name</label>
                        <input type="text" name="lname" placeholder="Last name" value="{{ old('lname') }}" required>
                    </div>
                </div>
                <div class="field input">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" value='{{ old('email') }}'
                        required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field input">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                </div>
                <div class="field image">
                    <label>Select Image</label>
                    <input type="file" accept="image/x-png,image/gif,image/jpeg,image/jpg" name="image" required>
                </div>
                <div class="field button">
                    <input type="submit" name="submit" value="Continue to Chat">
                </div>
            </form>
            <div class="link">Already signed up? <a href="{{ route('index') }}">Login now</a></div>
        </section>
    </div>

    <script src="{{ asset('js/app/pass-reveal.js') }}"></script>
@endsection
