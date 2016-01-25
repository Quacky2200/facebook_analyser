document.createElement('slides');
document.createElement('slide');
$(document).ready(function(){
	for(var i = 0; i < document.getElementsByTagName('select'); i++){
		document.getElementsByTagName('select')[i].disabled = false;
	}
	$('div.SlideControls input[value="Next"]').click(function(obj){
		var currentSlide = $(this).closest('slide');
		var currentSlideIndex = $('slides slide').index(currentSlide);
		if($(this).attr('type').toLowerCase() == 'submit'){
			var data = {};
			$('slides slide:eq(' + currentSlideIndex + ') section div').children('input').each(function(){
				if($(this).attr('name')){
					data[$(this).attr('name')] = $(this).attr('value');
				}
			});
			$.ajax({
				type: "POST",
				data: data,
				url: window.location,
				success: function(msg){
					alert(msg);
					if(msg == ''){
						$('slides slide:eq(' + (currentSlideIndex + 1) + ')').attr('class', 'active');
						currentSlide.attr('class', 'done');
					}
					else{
						//try{
						console.log(msg);
						var p = JSON.parse(msg);
						$('*.input.error').each(function(){
							for(x in p){
								if($(this).attr('class').indexOf(p[x]) > 0){
									if($('slides slide:eq(' + currentSlideIndex + ') section div input[name="' + p[x] + '"][type="submit"]').length === 1){
										$('*.input.error.' + p[x] + ' .details').remove();
										$('*.input.error.' + p[x]).append('<div class="details">' + p['details'] + '</div>');
									}
									$('*.input.error.' + p[x]).slideDown();
									return null;
								}
							}
							$(this).slideUp();
						});
					}
				}
			});
		}
		else{
			$('slides slide:eq(' + (currentSlideIndex + 1) + ')').attr('class', 'active');
			currentSlide.attr('class', 'done');
		}
		return false;
	});
	$('div.SlideControls input[value="Previous"]').click(function(obj){
		var currentSlide = $(this).closest('slide');
		var currentSlideIndex = $('slides slide').index(currentSlide);
		$('slides slide:eq(' + (currentSlideIndex - 1) + ')').attr('class', 'active');
		currentSlide.attr('class', '');
		return false;
	});
});