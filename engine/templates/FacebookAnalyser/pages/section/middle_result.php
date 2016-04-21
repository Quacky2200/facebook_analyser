	<nav class='topbar'>
		<div class='container'>
			<ul>
				<li>
					<?php echo "<a href='" . Engine::getRemoteAbsolutePath((new Home())->getURL()) . "'><b>FacebookAnalyser</b></a>";?>
				</li>
			</ul>
			<ul align='right'>
				<li>
					<?php echo "<a class='fblogin' style='float:right' href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "'>Account</a>";?>
				</li>
				<li>
					<?php echo "<a class='fblogin' style='float:right' href='" . User::instance()->getFacebookDeAuthURL(Engine::getRemoteAbsolutePath((new Home())->getURL())) . "'>Logout</a>";?>
				</li>
			</ul>
		</div>
	</nav>
	<main class="settings">
		<div class="container">
			<div class="result information">
				<h2 align="center">Your Result</h2>
				<h6 align="center">Created 10 minutes ago</h6>
				<div class="text">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>
				</div>
		</div>
	</main>