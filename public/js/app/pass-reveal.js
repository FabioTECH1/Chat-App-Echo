let passInput = $('input[type="password"]');
$(".fa-eye").on('click', e => {
    console.log(passInput.attr('type'));
    if (passInput.attr('type') == "password") {
        passInput.attr('type', 'text');
        $(".fa-eye").addClass('active');
    } else {
        passInput.attr('type', 'password');
        $(".fa-eye").removeClass('active');
    }
});