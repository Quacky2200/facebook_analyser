<header title>
	<span style='display:inline-block;margin:5% 0;'>
		<h1 style='margin:0'>FacebookAnalyser</h1>
		<div align='right'>
		</div>
	</span>
	<h3 id='analyse' style='font-weight: 100;text-align:center;margin:0'>Analysing</h3>
	<div class='loader' style='margin-top:0;'></div>	
	<div class='loaderDescription'>
		<noscript>
			<p align='center'>Please wait...</p>
		</noscript>
	</div>
</header>
<script>
//$(document).ready(function(){
	var urlRedirect = "";
	function scroller(){
		if($('.loaderDescription p').length > 1){
			$('.loaderDescription p:first').slideUp(400, function(){
				$('.loaderDescription p:first').remove();
				setTimeout(scroller, 250);
			});
		} else {
			if(urlRedirect != ""){
				window.location = urlRedirect;
			}
			setTimeout(scroller, 250);
		}
	}
	setTimeout(scroller, 250);
//});
</script>