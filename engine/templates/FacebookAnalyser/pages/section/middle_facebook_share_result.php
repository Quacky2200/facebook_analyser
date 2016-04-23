<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.6&appId=381088138591835";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your share button code -->
<div align='right'>
	<div class="fb-share-button" <?php echo "data-href='" . $this->result->Data['share-url'] . "'";?> data-layout="button" data-mobile-iframe="false"></div>
</div>