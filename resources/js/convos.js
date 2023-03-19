require("./bootstrap");

chatBox = $(".chat-box");
channel_name = $("body").attr("id");
var channel = Echo.channel(channel_name);

// check for incoming message and mount it
channel.listen(".message-sent", function (data) {
    $.ajax({
        url: `/readuser?id=${data.message.outgoing_id}`,
        type: "get",
        success: function (response) {
            // console.log(response);
            user = response;
            var userLink = $(".users-list").find(
                `.user_${data.message.outgoing_id}`
            );
            if (!userLink.length) {
                // check and prepend a message from a new user
                $(".users-list").prepend(`
                    <a href="/chats/${user.id}" class="user_${user.id}">
                    <div class="content">
                    <img src="http://127.0.0.1:8000/uploads/${user.profile_pic}">
                    <div class="details">
                    <span>${user.fname} ${user.lname}</span>
                    <p id="user_${user.id}" class="incoming">${data.message.message}
                    <span class="badge bg-primary badge-number ms-end" style="font-size:11px;margin-left:50px;">1</span>
                    </p></div></div></a>`);
            } else {
                console.log("here");

                outgoing = $(`#user_${data.message.outgoing_id}`).find(
                    ".outgoing"
                ).length;
                if (outgoing) {
                    console.log("still here");
                    $(`.user_${data.message.outgoing_id}`).html(
                        `<div class="content">
                        <img src="http://127.0.0.1:8000/uploads/${user.profile_pic}">
                        <div class="details">
                        <span>${user.fname} ${user.lname}</span>
                        <p id="user_${user.id}" class="incoming">${data.message.message}
                        <span class="badge bg-primary badge-number ms-end" style="font-size:11px;margin-left:50px;">1</span>
                        </p></div></div>`
                    );
                } else {
                    // handle message counter
                    lastMsg = data.message.message;
                    if (lastMsg.length > 22) {
                        lastMsg = lastMsg.substr(0, 20) + "....";
                    }

                    span = $(`.user_${user.id} span.badge-number`);
                    if (span.length) {
                        badgeNumber = span.text();
                        span.text(parseInt(badgeNumber) + 1);
                        span = span.clone();
                    } else {
                        span = `<span class="badge bg-primary badge-number ms-end"
                                style="font-size:11px;margin-left:50px;">1</span>`;
                    }
                    $(`#user_${user.id}`).text(lastMsg).append(span); // render new message
                }
            }
        },
        error: function (error) {
            console.log("error");
        },
    });

    //mark message as received
    $.ajax({
        url: $("#markReceived").attr("action"),
        type: "post",
        data: {
            _token: $('input[name="_token"]').val(),
            convo_id: data.message.conversation_id,
            id: data.message.outgoing_id,
        },
        success: function (response) {
            console.log("success");
        },
        error: function (error) {
            console.log("error");
        },
    });
});

// check recieved message and mark it recieved
channel.listen(".message-received", function (data) {
    outgoing = $(`#user_${data.receiver_id}.outgoing`).length;
    if (outgoing) {
        message_info = $(`#user_${data.reader_id} .fa-check`);
        if (message_info.length < 2) {
            message_info.append(
                `<i class="fa fa-check" aria-hidden="true"></i>`
            );
        }
    }
});

// check if data is read and mark it read
channel.listen(".message-read", function (data) {
    outgoing = $(`#user_${data.reader_id}.outgoing`).length;
    if (outgoing) {
        message_info = $(`#user_${data.reader_id} .fa-check`);
        if (message_info.length < 2) {
            message_info.append(
                `<i class="fa fa-check" aria-hidden="true"></i>`
            );
        }
        message_info.css("color", "blue");
    }
});

// search button
$(".search button").on("click", (e) => {
    if ($("#search-icon").attr("data-icon") == "ant-design:search-outlined") {
        $("#search-icon").attr("data-icon", "iconoir:cancel");
    } else {
        $("#search-icon").attr("data-icon", "ant-design:search-outlined");
    }
    $(".search input").toggleClass("show");
    $(".search button").toggleClass("active");
    $(".search input").focus();
    if ($(".search input").hasClass("active")) {
        $(".search input").val(" ");
        $(".search input").removeClass("active");
    }
});

// search for chats
$(".search input").on("keyup", (e) => {
    let search = $(".search input").val();
    urlink = $(".search").attr("urlink");
    let _token = $('input[name="_token"]').val();
    if (search != "") {
        $(".search input").addClass("active");
    } else {
        $(".search input").removeClass("active");
    }

    //post request
    $.ajax({
        url: urlink,
        type: "post",
        data: {
            _token: _token,
            search: search,
        },
        success: function (response) {
            // console.log(response);
            $(".users-list").html(response);
        },
    });
});

// let urlink = $("#getchat").attr("action");
// let _token = $('input[name="_token"]').val();
// let search = $(".search input");

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
