/*!
 * slideNswap Plugin for jQuery
 *
 * @author Asaf Zamir
 * @link http://jquery.kidsil.net
 * @version 0.01
 * @date 31/03/2011
 *
 * Description:
 * create a custom cursor for any element, no more .cur files
 * that may or may not work!
 * 
 * Usage:
 * just call customcursor($('body'),'image.jpg');
 * to make the custom cursor on the entire body, or choose another element
 * as you wish.
 * 
 * important footnote: this isn't perfect, I this it might be the rendering
 * of the browser, because I have had better experience with Chrome, and for 
 * some reason it was better moving sideways than up and down, but hey, it works :)
 * an example is available at http://jquery.kidsil.net
 */

function customcursor(element,imgUrl) {
	$('body').append('<img style="position:absolute;display:none;cursor:none;" id="mycursor" src="'+imgUrl+'" />');
	element.css('cursor','none');
	$(element).hover(function() {
		$('#mycursor').show();
	},function() {
		$('#mycursor').hide();		
	});
	$(element).mousemove(function(e){
			$('#mycursor').css('left', e.clientX - 1).css('top', e.clientY + 1);
	});
}
