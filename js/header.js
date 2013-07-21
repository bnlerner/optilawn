 $(window).resize(function() {
	$('#mainbody').css("height", $('#left_content').height()+50);
	$('#left_content').css("width", ($(window).width()-600)/2);
	$('#fright_content').css("width", ($(window).width()-600)/2);
    });

// ------------------
(function ($) {
	$.fn.vAlign = function(container) {
	return this.each(function(i){
	if(container == null) {
	container = 'div';
	}
	$(this).html("<" + container + ">" + $(this).html() + "</" + container + ">");
	var el = $(this).children(container + ":first");
	var elh = $(el).height(); //new element height
	var ph = $(this).height(); //parent height
	var nh = (ph - elh) / 2; //new height to apply
	$(el).css('margin-top', nh);
	
	});
	};
})(jQuery);
// ------------------

$(document).ready(function() {
	$("#logForm").validationEngine('attach',{scroll: false});
	$("#regForm").validationEngine('attach',{scroll: false});
	$('#mainbody').css("height", $('#left_content').height()+50);
	$('#left_content').css("width", ($(window).width()-600)/2);
	$('#fright_content').css("width", ($(window).width()-600)/2);

// ------------------LOGIN POPUP------------------------
$('a.poplight').click(function() {
    var popID = $(this).attr('rel');
    var popURL = $(this).attr('href');
    var query= popURL.split('?');
    var dim= query[1].split('&');
    var popWidth = dim[0].split('=')[1];

    $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="images/close_pop.png" class="btn_close" title="Close Popup" alt="Close" /></a>');

    var popMargTop = ($('#' + popID).height() + 80) / 2;
    var popMargLeft = ($('#' + popID).width() + 80) / 2;

    $('#' + popID).css({
        'margin-top' : -popMargTop,
        'margin-left' : -popMargLeft
    });

    $('body').append('<div id="fade"></div>');
	
    if($.browser.msie && $.browser.version=="6.0") {
    	$('img.btn_close').css({
        	'float': 'right',
    		'margin': '-20px -20px 0 0'});
    	$('#fade').css("height", $(window).height()*2);
    }
    else{
        $('img.btn_close').css({
        	'float': 'right',
    		'margin': '-55px -55px 0 0'});
    	$('#fade').css("height", '100%');
    };
    $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
    return false;
});
$('a.close').live('click', function() {
    $('#fade , .popup_login').fadeOut(function() {
    	$('#logForm').validationEngine('hideAll');
        $('#fade, a.close').remove();
    });
    return false;
});
// -----------------------------------------------------

});
