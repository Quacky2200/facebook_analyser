document.createElement('slides');
document.createElement('slide');
$(document).ready(function(){
	//When clicking on previous or next
	$('div.SlideControls input[type="submit"]').click(function(obj){
		var currentSlide = $(this).closest('slide');
		var currentSlideIndex = $('slides slide').index(currentSlide);
		if($(this).attr("value").toLowerCase() == "previous"){
			if((currentSlideIndex - 1) >= 0){
				$('slides slide:eq(' + (currentSlideIndex - 1) + ')').attr('class', 'active');
				currentSlide.attr('class', '');
			}
		} else {
			var serialized = $(this).closest('form').serializeArray();
			serialized.push({name:this.name, value:this.value});
		  	$.ajax({
				type: "POST",
				data: serialized,
				url: window.location,
				success: function(msg){
					var p = JSON.parse(msg);
					if(p["status"] == "ok"){
						if(p["details"] != null){
							window.location = p["details"];
						} else {
							$('slides slide:eq(' + (currentSlideIndex + 1) + ')').attr('class', 'active');
							$('slides slide:eq(' + (currentSlideIndex) + ') *.input.error').slideUp();
							currentSlide.attr('class', 'done');
						}
					} else if(p["status"] == "error"){
						$('*.input.error').each(function(){
							for(x in p["details"]){
								if($(this).attr('class').indexOf(p["details"][x]) > 0){
									$('*.input.error.' + p["details"][x]).slideDown();
									return null;
								}
							}
							$(this).slideUp();
						});
					} else {
						alert("Unknown JSON Action: " . p);
					}
				}
			});
		}
		return false;
	});
});