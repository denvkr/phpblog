/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
	
$(document).ready( function () {

});


    // смена оформления
    $(".bootstrap-theme, .non-responsive-switch").on('click', function() {
        //console.log('bootstrap-theme');
        skin = $(this).attr('data-skin');
        var cookie = $.cookie('bootstrap_theme');

        // переход на responsive
        //if (skin == 'non-responsive' && cookie == 'non-responsive')
        //    skin = 'bootstrap';

        $('#body_container').fadeOut('slow', function() {
            $('#bootstrap_theme').attr('href', 'css/' + skin + '.css');
        });
        console.log($('#bootstrap_theme').attr('href'));
        setTimeout(function() {
            $('#body_container').fadeIn();
        }, 1000);

        $.cookie('bootstrap_theme', skin, {
            path: '/'
        });
    });

/*
{
    url: "jquery.inputmask-3.x/js/phone-codes/phone-codes.json",
    countrycode:"+7",
    onKeyValidation: function () { //show some metadata in the console
        console.log($(this).inputmask("getmetadata")["cd"]);
    }
}
*/
   //formvalidator.validate('text');

//объект валидатор
var formvalidator= new Object();
//метод оценки поля
formvalidator.validate=function(fieldtype){
    switch (fieldtype) {
        case 'text':
            console.log('text');
            break;
        case 'number':
            console.log('number');
            break;
        default:
            console.log('default');       
    }
};

function InputCheck(){
	//btnprimaryCallback();
//проверяем введенные данные
if ($('#inputError1Status').length){
    html='<div id="infomessage" style="position:absolute;margin-top:20px;margin-left:25px;font-family: Arial, Helvetica, sans-serif;font-size:16px;">Введите корректный эл.адрес.</div>';
    //выводим сообщение               
    $.fancybox ({
                    width : 300,
                    height : 70,
                    overlayOpacity:0,
                    autoSize:false,
                    closeBtn:true,
                    content: html,
                    scrolling: 'no',
                    'beforeShow': function () {
                    },
                    'afterShow': function() {
                    },                                    
                    'afterClose': function() {
 
                    },
                    helpers : {
                        overlay : {
                        css : { 'overflow' : 'hidden' }
                        }
                    }
                });
    
} else
    document.getElementsByClassName('base_form')[0].submit();
};
