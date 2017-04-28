<?php

require 'inc.bootstrap.php';

if ( isset($_POST['mail']) ) {
	$user = get_user($_POST['mail']);
	if ( !$user ) {
		do_create_user($_POST['mail']);
	}
	elseif ( $user['secret'] ) {
		if ( isset($_POST['code']) ) {
			$userCcode = $_POST['code'];
			$secret = $user['secret'];
			$goodCodes = get_codes($secret);

			if ( in_array($userCcode, $goodCodes, true) ) {
				do_login($_POST['mail']);
				header('Location: index.php');
				exit;
			}

			echo '<p>Invalid code!</p>';
		}

		?>
		<form method="post" action>
			<input type="hidden" name="mail" value="<?= $_POST['mail'] ?>" />
			<p>Code: <input name="code" autofocus /></p>
			<p><button>Very code & log in</button></p>
		</form>
		<?php
		exit;
	}

	do_login($_POST['mail']);
	header('Location: index.php');
	exit;
}

?>
<form method="post" action>
	<p>Mail: <input type="email" name="mail" autofocus /></p>
	<p><button>Log in</button></p>
</form>
