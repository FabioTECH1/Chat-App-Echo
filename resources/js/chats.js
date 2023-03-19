require("./bootstrap");

$(() => scrollToBottom());
chatBox = $(".chat-box");
channel_name = $("body").attr("id");
var channel = Echo.channel(channel_name);

// check for incoming message and mount it
channel.listen(".message-sent", function (data) {
    if ($(".chat-area").attr("id") == `user-${data.message.outgoing_id}`) {
        $(".chat-box").append(`
    <div class="chat incoming">
    <div class="details">
    <p>${data.message.message}<br><span
    style="font-size:10px;">${data.timestamp}</span>
    </p></div></div>`);

        scrollToBottom();

        //mark message as received
        $.ajax({
            url: $("#markRead").attr("action"),
            type: "post",
            data: {
                _token: $('input[name="_token"]').val(),
                convo_id: data.message.conversation_id,
                id: data.message.outgoing_id,
            },
            success: function (response) {},
            error: function (error) {
                console.log("error");
            },
        });
    }
});

// check recieved message and mark it recieved
channel.listen(".message-received", function (data) {
    if ($(".chat-area").attr("id") == `user-${data.reciever_id}`) {
        $(".outgoing")
            .slice(-5)
            .each(function () {
                checkmark = $(this).find(".details p");
                if ($(this).find(".details p .fa-check").length < 1) {
                    checkmark.append(
                        `<i class="fa fa-check" aria-hidden="true"></i>`
                    );
                }
                if ($(this).find(".details p .fa-check").length < 2) {
                    //check if chat has not been marked recieved and mark it.
                    checkmark.append(
                        `<i class="fa fa-check" aria-hidden="true"></i>`
                    );
                }
            });
    }
});

// // check if data is read and mark it read
channel.listen(".message-read", function (data) {
    if ($(".chat-area").attr("id") == `user-${data.reader_id}`) {
        $(".outgoing")
            .slice(-5)
            .each(function () {
                checkmark = $(this).find(".details p");
                if ($(this).find(".details p .fa-check").length < 1) {
                    checkmark.append(
                        `<i class="fa fa-check" aria-hidden="true"></i>`
                    );
                }
                if ($(this).find(".details p .fa-check").length < 2) {
                    //check if chat has not been marked recieved and mark it.
                    checkmark.append(
                        `<i class="fa fa-check" aria-hidden="true"></i>`
                    );
                }
                $(this).find(".details p .fa-check").css("color", "blue");
            });
    }
});

// check message field to make active?
$(".message").focus();
$(".message").on("keyup", (e) => {
    message = $(".message").val().trim();
    if (message != "") {
        $(".typing-area button").addClass("active");
    } else {
        $(".typing-area button").removeClass("active");
    }
});

// send message and mount to chatbox
$(".typing-area button").on("click", (e) => {
    e.preventDefault();
    console.log("here");
    let urlink = $(".typing-area").attr("action");
    let _token = $('input[name="_token"]').val();
    let message = $(".message").val();
    var currentTime = new Date();
    var formattedTime = currentTime
        .toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "numeric",
            hour12: true,
        })
        .toLowerCase();

    // Remove leading and trailing whitespaces
    message = message.trim();
    if (!message || message === "") {
        return;
    }

    outgoing = `<div class="chat outgoing">
            <div class="details">
            <p>${message}<br><span style="font-size:10px;">
            ${formattedTime}</span>
            <i class="fa fa-clock-o" aria-hidden="true"
            style="padding-left:10px;"></i>
            </p></div></div>`;
    $(".chat-box").append(outgoing);
    $(".empty").text("");
    $(".message").val("");
    $(".typing-area button").removeClass("active");

    scrollToBottom();

    //post request
    $.ajax({
        url: urlink,
        type: "post",
        data: {
            _token: _token,
            message: message,
        },
        success: function (response) {
            message = response[0];
            // mount a check mark for message sent
            $(".outgoing:last .details")
                .html(`<p>${message.message}<br><span style="font-size:10px;">
            ${response.timestamp}</span><i class="fa fa-check" aria-hidden="true"
            style="padding-left:10px;"></i></p>`);
        },
    });
});

chatBox.on("mouseenter", function () {
    $(this).addClass("active");
});

chatBox.on("mouseleave", function () {
    $(this).removeClass("active");
});

function scrollToBottom() {
    chatBox.animate({ scrollTop: $(".chat-box")[0].scrollHeight }, "fast");
}

function OnlineEvent() {
    $.ajax({
        url: "/user-online",
        type: "post",
        data: {
            _token: $('input[name="_token"]').val(),
        },
        success: function (response) {
            // console.log(response);
        },
        error: function (response) {
            console.log("error");
        },
    });
}

setInterval(OnlineEvent, 10000);

function updateUserStatus(status) {
    $(".status").text(status);
}
// let timeout;
new_channel = "chat-" + window.location.href.split("/").pop();
console.log(new_channel);
var channel_2 = Echo.channel(new_channel);

channel_2.listen(".user-online", function (data) {
    updateUserStatus("Online");
    clearTimeout(timeout);
});

let timeout = setTimeout(() => {
    updateUserStatus("Offline");
}, 15000);
