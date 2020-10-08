jQuery(document).ready(function($){
	if($('.ctrl-templates.act-index').length) {
		$('.pull-right a[href*="/create"]').each(function(){
			$(this).before(' <a class="btn btn-danger btn-flat" title="" href="'+$(this).attr('href')+'#dde"><i class="fa fa-plus-square" data-original-title="" title=""></i>'+$(this).text()+' (Drag&Drop Editor)</a> ');
		});
	}
	if($('.ctrl-email_templates_gallery.act-index').length) {
		$('.pull-right a[href*="/create"]').each(function(){
			$(this).before(' <a class="btn btn-danger btn-flat" title="" href="'+$(this).attr('href')+'#dde"><i class="fa fa-plus-square" data-original-title="" title=""></i>'+$(this).text()+' (Drag&Drop Editor)</a> ');
		});
	}
});
jQuery(window).load(function(){
	if($('.ctrl-campaigns.act-template').length) {
		$('#cb-frame').before('<a class="btn btn-success" style="position:absolute;z-index:10001;"id="cb-frame-fullscreen"><i class="fa fa-arrows" data-original-title="Fullscreen" title="Fullscreen"></i></a>');
		$('#cb-frame-fullscreen').click(function(){
			if($(this).is('.done')){
				$('#cb-frame').css('position','static').css('height','800px').css('top','0').css('left','0').css('z-index','10000');
				$(this).removeClass('done').css('position','absolute').css('top','auto').css('left','auto');
			}
			else{
				$('#cb-frame').css('position','fixed').css('height','100%').css('top','0').css('left','0').css('z-index','10000');
				$(this).addClass('done').css('position','fixed').css('top','0').css('left','0');
			}
		});
	}
});