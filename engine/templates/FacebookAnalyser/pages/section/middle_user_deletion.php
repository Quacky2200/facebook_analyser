<?php require(__DIR__ . '/top_account_header.php');?>
<main class="settings">
	<div class="container">
		<div class='result information'>
			<div class='text'>
				<form method='POST' id='delete-account'>
					<br/>
					<h3 align='center'>Attention!</h3>
					<p>Deletion of your user account will remove all data present from our website including personal information (e.g. names, emails) and also your analyses you have created. Any analyses that have been shared will be invalidated. <b>This process is unrecoverable and data <i>will</i> be lost</b></p>
					<br/>
					<div id='controls'>
						<p>Are you sure you want to proceed?</p>
						<br/>
						<table style='width: 100%;'>
							<tr>
								<td align='left'><a <?php echo "href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "'";?>>No, take me back</a></td>
								<td align='right'><input type='submit' name='confirm' value='Yes, delete my account'></td>
							</tr>
						</table>
						<input type='hidden' name='action' value='confirm'/>
					</div>
				</form>
			</div>
		</div>
	</div>
</main>