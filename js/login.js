$(document).ready(function(){

    $('.loginform').hide().fadeIn(800);

    $('#show-register').on('click', function(e){
        e.preventDefault();
        switchForm('register');
    });

    $('#show-login').on('click', function(e){
        e.preventDefault();
        switchForm('login');
    });

    function switchForm(formType) {
        if (formType === 'register') {
            $('#login-form').removeClass('active').hide();
            $('#register-form').addClass('active').fadeIn(400);
            resetRegisterForm();
        } else {
            $('#register-form').removeClass('active').hide();
            $('#login-form').addClass('active').fadeIn(400);
            resetLoginForm();
        }
    }

    function resetLoginForm() {
        $('#Email').val('');
        $('#password').val('');
        $('#emailError').text('');
        $('#passwordError').text('');
        $("#btnLgn")
            .removeClass("activecolor")
            .addClass("inactive")
            .prop('disabled', true)
            .text('Login');
    }

    function resetRegisterForm() {
        $('#name').val('');
        $('#email').val('');
        $('#registerPassword').val('');
        $('#role').val('');
        $('#group').val('');
        $('#nameError').text('');
        $('#registerEmailError').text('');
        $('#registerPasswordError').text('');
        $('#roleError').text('');
        $('#groupError').text('');
        $("#btnRegister")
            .removeClass("activecolor")
            .addClass("inactive")
            .prop('disabled', true)
            .text('Register');
        $('#group-field').hide();
        $('#group').attr('required', false);
    }

    $(document).on("input", "#Email, #password", function(){
        validateLoginForm();
    });

    function validateLoginForm() {
        const email = $("#Email").val().trim();
        const password = $("#password").val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isEmailValid = email !== "" && emailRegex.test(email);
        const isPasswordValid = password !== "" && password.length >= 6;
        updateLoginButton(isEmailValid && isPasswordValid);
        updateLoginErrorMessages(email, password, emailRegex);
    }

    function updateLoginButton(isValid) {
        const $btn = $("#btnLgn");
        if (isValid) {
            $btn.removeClass("inactive")
                .addClass("activecolor")
                .prop('disabled', false);
        } else {
            $btn.removeClass("activecolor")
                .addClass("inactive")
                .prop('disabled', true);
        }
    }

    function updateLoginErrorMessages(email, password, emailRegex) {
        if (email === "") { $("#emailError").text(""); }
        else if (!emailRegex.test(email)) { $("#emailError").text("Invalid email format"); }
        else { $("#emailError").text(""); }

        if (password === "") { $("#passwordError").text(""); }
        else if (password.length < 6) { $("#passwordError").text("Password must be at least 6 characters"); }
        else { $("#passwordError").text(""); }
    }

    $(document).on("input change", "#name, #email, #registerPassword, #role, #group", function(){
        validateRegisterForm();
    });

    function validateRegisterForm() {
        const name = $("#name").val().trim();
        const email = $("#email").val().trim();
        const password = $("#registerPassword").val().trim();
        const role = $("#role").val();
        const group = $("#group").val().trim();

        const isNameValid = name !== "" && name.length >= 2;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isEmailValid = email !== "" && emailRegex.test(email);
        const isPasswordValid = password !== "" && password.length >= 6;
        const isRoleValid = role !== "";
        const isGroupValid = (role === "student") ? group !== "" : true;

        const isValid = isNameValid && isEmailValid && isPasswordValid && isRoleValid && isGroupValid;

        updateRegisterButton(isValid);
        updateRegisterErrorMessages(name, email, password, role, group, emailRegex);
    }

    function updateRegisterButton(isValid) {
        const $btn = $("#btnRegister");
        if (isValid) {
            $btn.removeClass("inactive")
                .addClass("activecolor")
                .prop('disabled', false);
        } else {
            $btn.removeClass("activecolor")
                .addClass("inactive")
                .prop('disabled', true);
        }
    }

    function updateRegisterErrorMessages(name, email, password, role, group, emailRegex) {
        if (name === "") { $("#nameError").text(""); }
        else if (name.length < 2) { $("#nameError").text("Name must be at least 2 characters"); }
        else { $("#nameError").text(""); }

        if (email === "") { $("#registerEmailError").text(""); }
        else if (!emailRegex.test(email)) { $("#registerEmailError").text("Invalid email format"); }
        else { $("#registerEmailError").text(""); }

        if (password === "") { $("#registerPasswordError").text(""); }
        else if (password.length < 6) { $("#registerPasswordError").text("Password must be at least 6 characters"); }
        else { $("#registerPasswordError").text(""); }

        if (role === "") { $("#roleError").text("Please select a role"); }
        else { $("#roleError").text(""); }

        if (role === "student" && group === "") { $("#groupError").text("Please enter your group"); }
        else { $("#groupError").text(""); }
    }

    $('#role').on('change', function() {
        if ($(this).val() === 'student') {
            $('#group-field').fadeIn(200);
            $('#group').attr('required', true);
        } else {
            $('#group-field').fadeOut(200);
            $('#group').attr('required', false);
        }
        validateRegisterForm();
    });

    $("#btnLgn").on('click', function(){
        if(!$(this).hasClass('inactive')) {
            handleLogin();
        }
    });

    function handleLogin() {
        const $btn = $("#btnLgn");
        $('input, button').prop('disabled', true);
        $btn.addClass('loading').text('Signing in...');
        setTimeout(function(){
            $btn.removeClass('loading').addClass('success').text('Login Successful!');
            setTimeout(function(){ window.location.href = 'attendenci.html'; }, 1000);
        }, 1500);
    }

    $("#btnRegister").on('click', function(){
        if(!$(this).hasClass('inactive')) {
            handleRegister();
        }
    });

    function handleRegister() {
        const $btn = $("#btnRegister");
        $('input, select, button').prop('disabled', true);
        $btn.addClass('loading').text('Creating account...');
        setTimeout(function(){
            $btn.removeClass('loading').addClass('success').text('Registration Successful!');
            setTimeout(function(){
                switchForm('login');
                $('input, select, button').prop('disabled', false);
            }, 1500);
        }, 1500);
    }

    $(document).on('keypress', function(e){
        if(e.which === 13) {
            e.preventDefault();
            if($('#login-form').hasClass('active') && !$("#btnLgn").hasClass('inactive')) { $("#btnLgn").click(); }
            else if($('#register-form').hasClass('active') && !$("#btnRegister").hasClass('inactive')) { $("#btnRegister").click(); }
        }
    });

    
    validateLoginForm();
    validateRegisterForm();

});
