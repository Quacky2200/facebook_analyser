<main class="centerstage">
	<header title>
		<span style='display:inline-block;margin:5% 0;'>
			<h1 style='margin:0;font-weight:400;letter-spacing:-5px'>FacebookAnalyser</h1>
			<div align='right'>
			</div>
		</span>
		<div class='loader' style='margin-top:0;'></div>	
		<div class='loaderDescription'>
			<noscript>Please wait...</noscript>
		</div>
	</header>
	<script id='remove'>
		var urlRedirect = "";
		var factor = 1;
		var speedUpSpeed = 50;
		var updateSpeed = 250;
		var animationSpeed = 500 + speedUpSpeed;
		function scroller(){
			factor = $('.loaderDescription p').length * speedUpSpeed;
			if($('.loaderDescription p').length > 1){
				$('.loaderDescription p:first').css({opacity: 0, transition: 'opacity 0.25s'});
				$('.loaderDescription p:first').slideUp(animationSpeed - factor, function(){
					$('.loaderDescription p:first').remove();
					setTimeout(scroller, updateSpeed - factor);
				});
			} else {
				if(urlRedirect != ""){
					window.location = urlRedirect;
					return;
				}
				setTimeout(scroller, updateSpeed - factor);
			}
		}
		scroller();
		scriptCleanupService();
		function scriptCleanupService(){
			$('script#remove').remove();
			setTimeout(scriptCleanupService, updateSpeed - factor);
		}
	</script>
</main>