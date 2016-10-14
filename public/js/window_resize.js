/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function dom_elm_sizing() {

	var Width = window.innerWidth;
	var Height = window.innerHeight;
	
        //console.log($(".bg-info:eq(0)").css('font-size')+' '+Width+' '+Height);
	if (Width < 768 && Width > 360) {
                //уменшаем размер шрифта на кнопках
		$(".bg-info:eq(0)").css('font-size','16px');
                $(".bg-info:eq(0)").css('font-weight','bold');
                $(".base_form:eq(0)").css('width','350px');
                //console.log($(".bg-info:eq(0)").css('font-size'));
        } else if (Width <= 360){
            if (Width <= 320){
                $(".base_form:eq(0)").css('width','320px');
            } else
                 $(".base_form:eq(0)").css('width','350px');               
		$(".bg-info:eq(0)").css('font-size','12px');
                $(".bg-info:eq(0)").css('font-weight','bold');
	} else if (Width >= 768) {
		$(".bg-info:eq(0)").css('font-size','36px');
                $(".bg-info:eq(0)").css('font-weight','normal');
                $(".base_form:eq(0)").css('width','350px');
                //console.log($(".bg-info:eq(0)").css('font-size'));
	}
}


