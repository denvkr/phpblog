$(function() {

	//Развернуть репосты
	$(".show-more-reposts").click(function() {
		switch($(this).html()) {

			case 'Развернуть':
			$(this).parents().find(".hidden-repost-items").fadeIn();
			$(this).html("Свернуть");
			break;

			case 'Свернуть':
			$(this).parents().find(".hidden-repost-items").fadeOut();
			$(this).html("Развернуть");
			break;

			default:
			$(this).parents().find(".hidden-repost-items").fadeOut();
			$(this).html("Развернуть");
			break;
		}
	});

	//Продвинуть стих
	$(".poem-app").click(function() {
		$(".poem-app-popup-wrap").stop().fadeToggle();
		return false;
	});
        
	$(".poem-app-popup button").click(function() {
		$(".poem-app-popup-wrap").fadeOut();
	});

	//Плагиат
	$(".plagiat").hide();
	$(".main-post-item").hover(function() {
		$(this).find(".plagiat").stop().fadeToggle();
	});

	//Сортировка ленты
	$(".feed-sorting-item").click(function() {
		$(this).parent().find(".feed-sorting-item").not(this).removeClass("active");
		$(this).addClass("active");
	});

	//Дополнительные теги
	$(".profile-favorite-item-close").click(function() {
		$(this).parent().hide();
	});

	//Слайдер
	$(".bxslider").bxSlider({
		pager: false,
		prevSelector: $(".prev"),
		nextSelector: $(".next"),
		prevText: "",
		nextText: "",
		adaptiveHeight: true
	});

	//Попап окна
	$(".popup").magnificPopup();

	//Стилизация форм
	//$('input[type="checkbox"]').styler();//,select

	//Автоматическое выравнивание высоты блоков при изменении высоты одного из них
	$(".rating-poem-name").equalHeights();
	$(".classic .user-rating-list-item-name").equalHeights();
	$(".users .rating-list-item-center").equalHeights();
	$(".groups .rating-list-item-center").equalHeights();

	//Табы
	$(".tab_item").not(":first").hide();
	$(".wrapper .tab").click(function() {
		$(".wrapper .tab").removeClass("active").eq($(this).index()).addClass("active");
		$(".tab_item").hide().eq($(this).index()).fadeIn()
	}).eq(0).addClass("active");

	//Исчезание элемента при клике вне его области
	$(document).click(function(e) {
		var elem = $(".poem-app-popup-wrap");
		if (e.target != elem[0] && !elem.has(e.target).length) {
			elem.fadeOut();
		}
	});

	//Плавная прокрутка в Chrome
	try {
		$.browserSelector();
		if($("html").hasClass("chrome")) {
			$.smoothScroll();
		}
	} catch(err) {

	};

	//Запрет перемещения изображений
	$("img, a").on("dragstart", function(event) { event.preventDefault(); });

});


