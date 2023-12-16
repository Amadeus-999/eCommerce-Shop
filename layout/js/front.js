$(function(){

    'use strict';

    //Switch Between Login y Signup
    $('.login-page h1 span').click(function(){
        $(this).addClass('selected').siblings().removeClass('selected');

        $('.login-page form').hide();

        $('.' + $(this).data('class')).fadeIn(100);
    });

    //Trigger para utilizar el Selectboxtit
    $("select").selectBoxIt({
        autoWidth: false
    });

    //Ocultar Placeholder en Form
    $('[placeholder]').focus(function(){
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    }).blur(function(){
        $(this).attr('placeholder',$(this).attr('data-text'));
    });

    //Agregar asterisco al archivo required
    $('input').each(function(){
        if($(this).attr('required') === 'required'){
            $(this).after('<span class="asterisk">*</span>');
        }
    });

    //Mensaje de Confirmacion 'Delete'
    $('.confirm').click(function(){
        return confirm('¿Estas seguro?');
    });


    ///Live Preview newad
    $('.live').keyup(function(){
        $($(this).data('class')).text($(this).val());
    });
   

});