<?php

require 'inc.bootstrap.php';

do_logincheck();

// $uri = get_authenticator_uri($_SESSION['2fa']['mail'], get_secret($_SESSION['2fa']['mail']));
// echo "<pre>$uri</pre>";

?>
<p><img src="qr.php" /></p>

<p><a href="logout.php">Log out to try</a></p>

<p>Valid codes:</p>

<pre><? print_r(get_codes(get_secret($_SESSION['2fa']['mail']))) ?></pre>
