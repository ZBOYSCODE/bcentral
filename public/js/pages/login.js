/*
 *  Document   : login.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Login page
 */

var Login = function() {

    // Function for switching form views (login, reminder and register forms)
    var switchView = function(viewHide, viewShow, viewHash){
        viewHide.slideUp(250);
        viewShow.slideDown(250, function(){
            $('input').placeholder();
        });

        if ( viewHash ) {
            window.location = '#' + viewHash;
        } else {
            window.location = '#';
        }
    };

    return {
        init: function() {
            /* Switch Login, Reminder and Register form views */
            var formLogin       = $('#form-login'),
                formReminder    = $('#form-reminder'),
                formRegister    = $('#form-register');

            $('#link-register-login').click(function(){
                switchView(formLogin, formRegister, 'register');
            });

            $('#link-register').click(function(){
                switchView(formRegister, formLogin, '');
            });

            $('#link-reminder-login').click(function(){
                switchView(formLogin, formReminder, 'reminder');
            });

            $('#link-reminder').click(function(){
                switchView(formReminder, formLogin, '');
            });

            // If the link includes the hashtag 'register', show the register form instead of login
            if (window.location.hash === '#register') {
                formLogin.hide();
                formRegister.show();
            }

            // If the link includes the hashtag 'reminder', show the reminder form instead of login
            if (window.location.hash === '#reminder') {
                formLogin.hide();
                formReminder.show();
            }


            $.validator.addMethod("loginRegex", function(value, element) {
                return this.optional(element) || /^[a-z áéíóúñüàè]+$/i.test(value);
            }, "Nombre no válido");            

            /*
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Login form - Initialize Validation */
            $('#form-login').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    e.closest('.form-group').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'login-email': {
                        required: true,
                        email: true
                    },
                    'login-password': {
                        required: true,
                    }
                },
                messages: {
                    'login-email': 'Por favor, ingrese su email.',
                    'login-password': {
                        required: 'Por favor, ingrese su contraseña.',
                    }
                }           
            });

            /* Reminder form - Initialize Validation */
            $('#form-reminder').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    e.closest('.form-group').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'reminder-email': {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    'reminder-email': 'Por favor, ingrese su email.'
                },
                onsubmit: true,
                submitHandler: function(form){
                    console.log(form);
                    $.ajax({
                        url: 'auth/forgot_password',
                        type: 'POST',
                        dataType: 'json',
                        data: {email: $('#reminder-email').val()}
                    })
                    .done(function(response) {
                        console.log("success");
                        if(response){
                            switchView(formReminder, formLogin, '');                           
                            $('#form-reminder')[0].reset();
                            alert("Se ha enviado tu nueva contraseña!");                            
                        } else{
                            alert("Hubo un error");
                        }

                    })
                    .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log(XMLHttpRequest);
                            console.log(textStatus);
                            console.log(errorThrown);
                    })
                    .always(function() {
                        console.log("complete");
                    });
                }                
            });

            /* Register form - Initialize Validation */
            $('#form-register').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    if (e.closest('.form-group').find('.help-block').length === 2) {
                        e.closest('.help-block').remove();
                    } else {
                        e.closest('.form-group').removeClass('has-success has-error');
                        e.closest('.help-block').remove();
                    }
                },
                rules: {
                    'register-firstname': {
                        required: true,
                        minlength: 2,
                        loginRegex: true,
                    },
                    'register-lastname': {
                        required: true,
                        minlength: 2,
                        loginRegex: true,
                    },
                    'register-email': {
                        required: true,
                        email: true,
                        remote: {
                            url: 'auth/checkEmail',
                            type: 'post'
                        }
                    },
                    'register-password': {
                        required: true,
                        minlength: 5
                    },
                    'register-password-verify': {
                        required: true,
                        equalTo: '#register-password'
                    },
                    'register-terms': {
                        required: true
                    }
                },
                messages: {
                    'register-firstname': { 
                        required: 'Por favor ingrese su nombre.',
                        minlength: 'Por favor ingrese su nombre.',
                        loginRegex: 'Formato de nombre no válido.'
                    },
                    'register-lastname': {
                        required: 'Por favor ingrese su apellido.',
                        minlength: 'Por favor ingrese su apellido.',
                        loginRegex: 'Formato de apellido no válido.'
                    },
                    'register-email': {
                        required: 'Por favor, ingrese un email.',
                        email: 'Este no es un email válido.',
                        remote: 'Este email ya ha sido utilizado.'
                    },
                    'register-password': {
                        required: 'Por favor, ingrese una contraseña.',
                        minlength: 'La contraseña debe ser mayor a 5 caracteres.'
                    },
                    'register-password-verify': {
                        required: 'Por favor, repita contraseña.',
                        minlength: 'La contraseña debe ser mayor a 5 caracteres.',
                        equalTo: 'Las contraseñas no coinciden.'
                    },
                    'register-terms': {
                        required: 'Por favor, acepte los términos para continuar.'
                    }
                },
                onsubmit: true,
                submitHandler: function(form){
                    console.log(form);
                    $.ajax({
                        url: 'auth/register',
                        type: 'POST',
                        dataType: 'json',
                        data: {firstname: $('#register-firstname').val(),
                               lastname: $('#register-lastname').val(),
                               email: $('#register-email').val(),
                               password: $('#register-password').val(),
                               confirm_password: $('#register-password-verify').val(),
                               terms: $('#register-terms').val() }                               
                    })
                    .done(function(response) {
                        console.log("success");
                        if(response){
                            switchView(formRegister, formLogin, '');                            
                            $('#form-register')[0].reset();
                            alert(response.msg);                            
                        } else{
                            alert("Hubo un error");
                        }

                    })
                    .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log(XMLHttpRequest);
                            console.log(textStatus);
                            console.log(errorThrown);
                    })
                    .always(function() {
                        console.log("complete");
                    });
                    
                }

            });
        }
    };
}();