// var script = document.createElement('script');
// script.src = ;
if (typeof jQuery=='undefined'){
	script = document.createElement('script');
	script.src = "http://code.jquery.com/jquery-1.11.0.min.js";
	document.getElementsByTagName('head')[0].appendChild(script);
	defer(function(){
		$.get('index.html', function(data){
			content = $('body').html();
			$('html').html('');
			document.write(data);
			setTimeout(function(){
				$('#content').append(content);
				$('#ajax_content').fadeIn(600);
				$('html, body').animate({
          			scrollTop: $('#content').offset().top
        		}, 1000);
			},80);
      });
	});
	
}
function defer(method) {
    if (window.$)
        method();
    else
        setTimeout(function() { defer(method) }, 50);
}