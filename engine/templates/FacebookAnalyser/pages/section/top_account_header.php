<nav class='topbar'>
	<div class='container'>
		<ul>
			<li>
				<?php echo "<a href='" . Engine::getRemoteAbsolutePath((new Home())->getURL()) . "'><img id='logo' src='" . Engine::getRemoteAbsolutePath($template->getLocalDir() . '/public/images/white-logo-transparent.png') . "' height='32' style='padding: 5px;'/><b>FacebookAnalyser</b></a>";?>
			</li>
		</ul>
		<?php if(User::instance()->isLoggedIn()){ ?>
			<ul align='right'>
				<li>
					<?php echo "<a class='fblogin' style='float:right' href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "'>Account</a>";
					?>
				</li>
				<li>
					<?php echo "<a class='fblogin' style='float:right' href='" . User::instance()->getFacebookDeAuthURL(Engine::getRemoteAbsolutePath((new Home())->getURL())) . "'>Logout</a>";?>
				</li>
			</ul>
		<?php } ?>
	</div>
</nav>