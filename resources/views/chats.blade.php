@extends('layout.layout')
@section('title', ' Chats | Chat-Wink')


@section('content')

    <div class="wrapper">
        <section class="chat-area" id="user-{{ $user->id }}">
            <header>
                <a href="{{ route('convos') }}" class="back-icon">
                    <i class="fa fa-backward" aria-hidden="true"
                        style="color: #212529; font-size:22px; padding-right:5px;"></i>
                </a>
                <img src="{{ asset('uploads/' . $user->profile_pic) }}" alt="">
                <div class="details">
                    <span>{{ $user->fname }} {{ $user->lname }}</span>
                    <p class="status" style="color:rgb(138, 132, 132)"></p>
                </div>
            </header>
            <form action="{{ route('message_read') }}" hidden id="markRead"></form>
            <div class="chat-box">
                @php
                    $datetime = '';
                @endphp
                @forelse ($messages as $message)
                    @if ($datetime != date('Y-m-d', strtotime($message->created_at)))
                        <p style="text-align: center">
                            @if (Carbon\Carbon::parse($message->created_at)->isToday())
                                @php $datetime = date('Y-m-d', strtotime($message->created_at)); @endphp
                                Today
                            @elseif (
                                !Carbon\Carbon::parse($message->created_at)->isToday() &&
                                    (Carbon\Carbon::parse($message->created_at)->diffInDays(Carbon\Carbon::now()) == 0 ||
                                        Carbon\Carbon::parse($message->created_at)->diffInDays(Carbon\Carbon::now()) == 1))
                                @php $datetime = date('Y-m-d', strtotime($message->created_at)); @endphp
                                Yesterday
                            @elseif(Carbon\Carbon::parse($message->created_at)->diffInDays(Carbon\Carbon::now()) > 6 ||
                                    date('l', strtotime($message->created_at)) == date('l'))
                                @php $datetime = date('Y-m-d', strtotime($message->created_at)); @endphp
                                {{ date('d F Y', strtotime($message->created_at)) }}
                            @else
                                @php $datetime = date('Y-m-d', strtotime($message->created_at)); @endphp
                                {{ date('l', strtotime($message->created_at)) }}
                            @endif
                        </p>
                    @endif
                    @if ($message->outgoing_id == auth()->id())
                        <div class="chat outgoing">
                            <div class="details">
                                <p>{{ $message->message }}<br><span style="font-size:10px;">
                                        {{ date('g:i a', strtotime($message->created_at)) }}
                                    </span>
                                    <i class="fa fa-check" aria-hidden="true"
                                        style="padding-left:10px; @if ($message->status == 2) color: blue; @endif"></i>
                                    @if ($message->status == 1)
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    @elseif($message->status == 2)
                                        <i class="fa fa-check" aria-hidden="true" style="color: blue;"></i>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="chat incoming">
                            <div class="details">
                                <p>{{ $message->message }}<br><span
                                        style="font-size:10px;">{{ date('g:i a', strtotime($message->created_at)) }}</span>
                                </p>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text empty">No messages are available. Once you send message they will appear here.</div>
                @endforelse
            </div>
            <form action="{{ route('message', $user->id) }}" urlink="{{ route('getmessage', $user->id) }}"
                class="typing-area" method="post">
                @csrf
                <input class='message' type="text" name="message" class="input-field"
                    placeholder="Type a message here..." autocomplete="off">
                {{-- <input type="submit" value=""> --}}
                <button>
                    <i class="fa fa-paper-plane" aria-hidden="true" style="font-size: 25px"></i>
                </button>
            </form>
        </section>
    </div>

    <script src="{{ asset('js/app/chats.js') }}"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
@endsection
