require("./bootstrap");

var channel = Echo.channel("chat");
channel.listen(".message-sent", function (data) {
    alert(JSON.stringify(data));
    console.log(data.message);
    $(".chat-box").append(`
    <div class="chat incoming">
    <div class="details">
    <p>${data.message}<br><span
    style="font-size:10px;">${data.timestamp}</span>
    </p></div></div>`);

    $(".chat-box").scrollTop($(".chat-box")[0].scrollHeight);
});

var channel1 = Echo.channel("convo");
channel1.listen(".message-received", function (data) {
    alert(JSON.stringify(data));
    $(`#user-${data.receiver_id} .message-info`).append(
        `<i class="fa fa-check" aria-hidden="true"></i>`
    );
});

channel1.listen(".message-read", function (data) {
    alert(JSON.stringify(data));
    $(`#user-${data.reader_id} .message-info .fa-check`).css("color", "blue");
});
