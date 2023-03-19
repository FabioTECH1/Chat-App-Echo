@extends('layout.layout')
@section('title', 'Login | Chat-Wink')

@section('content')
    <div class="wrapper">
        <section class="form login">
            <header>Chat-Wink</header>
            <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @if (Session::has('creds_error'))
                    <div class="error-text" style="display: block">Invalid Login details</div>
                @endif
                <div class="field input">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email"
                        @if (Session::has('creds_error')) value="{{ Session::get('creds_error') }}" @endif required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field button">
                    <input type="submit" name="submit" value="Continue to Chat">
                </div>
            </form>
            <div class="link">Not yet signed up? <a href="{{ route('register') }}">Signup now</a></div>
        </section>
    </div>
    <script src="{{ asset('js/app/pass-reveal.js') }}"></script>


@endsection
