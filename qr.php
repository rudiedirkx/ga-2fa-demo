<?php

require 'inc.bootstrap.php';

if ( !isset($_SESSION['2fa']['mail'], $_SESSION['2fa']['secret']) ) {
	return;
}

$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(256);
$renderer->setWidth(256);
$writer = new \BaconQrCode\Writer($renderer);

$uri = get_authenticator_uri($_SESSION['2fa']['mail'], $_SESSION['2fa']['secret']);
// exit($uri);

header('Content-type: image/png');
echo $writer->writeString($uri);
