<?php

require 'inc.bootstrap.php';

do_logincheck();

$user = get_user();

if ( isset($_SESSION['2fa']['secret'], $_POST['code']) ) {
	$goodCodes = get_codes($_SESSION['2fa']['secret']);
	if ( in_array($_POST['code'], $goodCodes, true) ) {
		$user['secret'] = $_SESSION['2fa']['secret'];

		$db = get_db();
		$db['users'][ $user['mail'] ] = $user;
		do_save($db);

		header('Location: index.php');
		exit;
	}

	echo '<p>Invalid code. Try again.</p>';
}

$_SESSION['2fa']['secret'] = $user['secret'] ?: get_rand_string();

// $uri = get_authenticator_uri($_SESSION['2fa']['mail'], $_SESSION['2fa']['secret']);
// exit($uri);

?>
<p><img src="qr.php" /></p>

<? if ( !$user['secret'] ): ?>
	<form method="post">
		<p>Code: <input name="code" autofocus /></p>
		<p><button>Verify</button></p>
	</form>
<? else: ?>
	<p><a href="logout.php">Log out to try</a></p>

	<p>Valid codes:</p>
	<pre><? print_r(get_codes(get_secret($_SESSION['2fa']['mail']))) ?></pre>
<? endif ?>
