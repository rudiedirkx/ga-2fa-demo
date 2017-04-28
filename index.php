<?php

require 'inc.bootstrap.php';

do_logincheck();

?>
<h1>Logged in</h1>

<pre><?php

print_r(get_user());

?></pre>

<p><a href="activate.php">Activate 2FA</a></p>
