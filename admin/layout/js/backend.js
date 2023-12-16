$(function(){

    'use strict';

    //Dashboard
    $('.toogle-info').click(function(){
        $(this).toggleClass('selected').parent().next('.panel-body').fadeToggle(200);
        if($(this).hasClass('selected')){
            $(this).html('<i class="fa fa-plus fa-lg"></i>');
        }else{
            $(this).html('<i class="fa fa-minus fa-lg"></i>');
        }
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

    //Comvierte el campo de Password en Hover
    var passField = $('.password')
    $('.show-pass').hover(function(){
        passField.attr('type', 'text');
    }, function(){
        passField.attr('type', 'password');
    });

    //Mensaje de Confirmacion 'Delete'
    $('.confirm').click(function(){
        return confirm('¿Estas seguro?');
    });

    //Opcion para ver categorias
    $('.cat h3').click(function(){
        $(this).next('.full-view').fadeToggle(200);
    });

    $('.option span').click(function(){
        $(this).addClass('active').siblings('span').removeClass('active');

        if($(this).data('view') == 'full'){
            $('.cat .full-view').fadeIn(200);
        }else{
            $('.cat .full-view').fadeOut(200);
        }
    });


   
    $('.child-link').hover(function(){
        $(this).find('.show-delete').fadeIn(400);
    }, function(){
        $(this).find('.show-delete').fadeOut(400);
    });

});