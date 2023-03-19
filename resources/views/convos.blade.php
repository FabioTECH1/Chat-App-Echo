@extends('layout.layout')
@section('title', 'Home | Chat-Wink')


@section('content')
    <div class="wrapper">
        <section class="users" value="{{ auth()->id() }}">
            <header>
                <div class="content">
                    {{-- <img src="{{ asset('uploads/' . auth()->user()->profile_pic) }}" alt=""> --}}
                    <div class="details">
                        <span>{{ auth()->user()->fname }} {{ auth()->user()->lname }}</span>
                        <p style="color:rgb(138, 132, 132)">{{ auth()->user()->status }}</p>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="logout">Logout</a>
            </header>
            <form action="{{ route('message_received') }}" hidden id="markReceived">
                @csrf</form>

            <div class="search" urlink='{{ route('search') }}'>
                <span class="text">Select a user to start chat</span>
                <input type="text" placeholder="Enter name to search...">
                <button>
                    <span class="iconify" id="search-icon" data-icon="ant-design:search-outlined"></span>
                </button>
            </div>
            <style>
                .users-list a {
                    text-decoration: none;
                }
            </style>
            <div class="users-list">
                @foreach ($conversations as $conversation)
                    @php
                        if ($conversation->user_1 == auth()->id()) {
                            $user_id = $conversation->user_2;
                        } else {
                            $user_id = $conversation->user_1;
                        }
                        
                    @endphp
                    <a href="{{ route('chats', $user_id) }}" class="user_{{ $user_id }}">
                        <div class="content">
                            <img src="{{ asset('uploads/' . $conversation->user2->profile_pic) }}">
                            <div class="details">
                                <span>
                                    @if ($conversation->user_1 == auth()->id())
                                        {{ $conversation->user2->fname }} {{ $conversation->user2->lname }}
                                    @else
                                        {{ $conversation->user1->fname }} {{ $conversation->user1->lname }}
                                    @endif
                                </span>
                                @php
                                    $last_message = $conversation->latest_message->message;
                                    if (strlen($last_message) > 22) {
                                        $last_message = substr($last_message, 0, 20) . '....';
                                    }
                                @endphp

                                @if (auth()->user()->id == $conversation->latest_message->outgoing_id)
                                    <p id="user_{{ $user_id }}" class="outgoing">You:
                                        {{ $last_message }}
                                        <i class="fa fa-check" aria-hidden="true"
                                            style="padding-left:10px; @if ($conversation->latest_message->status == 2) color: blue; @endif"></i>
                                        @if ($conversation->latest_message->status == 1)
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        @elseif($conversation->latest_message->status == 2)
                                            <i class="fa fa-check" aria-hidden="true" style="color: blue;"></i>
                                        @endif
                                    </p>
                                @else
                                    <p id="user_{{ $user_id }}" class="incoming">
                                        {{ $last_message }}
                                        @if ($conversation->unread_messages() != 0)
                                            <span class="badge bg-primary badge-number ms-end"
                                                style="font-size:11px;margin-left:50px;">
                                                {{ $conversation->unread_messages() }}
                                            </span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="status-dot"><i class="fas fa-circle"></i></div> --}}
                    </a>
                @endforeach
            </div>
        </section>
    </div>
    <script src="{{ asset('js/app/convos.js') }}"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}

@endsection
