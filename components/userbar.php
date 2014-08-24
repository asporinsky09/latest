<?php 
include_once 'inc/functions.php';
include_once 'inc/dbConnect.php';
include_once 'inc/Session.php';

	SecureSession::create();
	$logged_in = login_check($db);
?>
<?php if (! $logged_in) { ?>
<form name="sign-in" action="inc/process_login.php" method="POST" class="login">
				<fieldset>Welcome back</fieldset>
				<input type="email" name="email" placeholder="Email Address">
				<!-- <span class="inside-button-wrapper"> -->
					<input type="text" name="password" placeholder="Password:" class="inside-button-outer">
					<!-- <button id="forgot" type="button" class="inside-button-button">?</button> -->
				<!-- </span> -->
				<button type="submit" class="btn-login" onclick="secureSend(this.form, this.form.password);">Log in</button>
			</form>
			<?php } else { ?>
			<a href="inc/logout.php">Log Out</a>
			<?php } ?>
			<?php
			    if (isset($_GET['error'])) {
			        echo '<p class="error">Error Logging In!</p>';
			    }
			?>