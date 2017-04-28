<?php

use Base32\Base32;

define('PROJECT_DB_FILE', 'db.json');

function do_logincheck( $redirect = true ) {
	if ( empty($_SESSION['2fa']['mail']) || !get_user($_SESSION['2fa']['mail']) ) {
		if ( $redirect ) {
			header('Location: login.php');
		}

		exit('Access denied');
	}
}

function do_login($mail) {
	$_SESSION['2fa']['mail'] = $mail;
}

function do_logout() {
	unset($_SESSION['2fa']);
}

function do_create_user( $mail ) {
	$db = get_db();
	$db['users'][$mail] = ['secret' => ''];
	do_save($db);
}

function do_save($db) {
	return file_put_contents(PROJECT_DB_FILE, json_encode($db));
}

function get_authenticator_uri($mail, $secret) {
	return 'otpauth://totp/' . PROJECT_2FA_LABEL . ':' . $mail . '?secret=' . Base32::encode($secret) . '&issuer=' . PROJECT_2FA_LABEL;
}

function get_rand_string($length = 12) {
	$source = implode(range('A', 'Z')) . implode(range(0, 9)) . implode(range('a', 'z'));
	$string = '';
	while ( strlen($string) < $length ) {
		$string .= $source[rand(0, strlen($source) - 1)];
	}
	return $string;
}

function get_secret($mail) {
	$db = get_db();
	$user = get_user($mail);

	if ( $user && $user['secret'] ) {
		return $user['secret'];
	}

	$secret = get_rand_string();
	$db['users'][$mail]['secret'] = $secret;
	do_save($db);
	return $secret;
}

function get_code($secret, $count) {
	$bin_counter = pack('N*', 0, $count);
	$hash = hash_hmac('sha1', $bin_counter, $secret, true);

	$offset = ord($hash[19]) & 0xf;
	$temp = unpack('N', substr($hash, $offset, 4));
	return str_pad(substr($temp[1] & 0x7fffffff, -6), 6, '0', STR_PAD_LEFT);
}

function get_codes($secret) {
	$codes = [];
	for ( $offset = -3; $offset <= 3; $offset++ ) {
		$codes[] = get_code($secret, (int)(time()/30 + $offset));
	}

	return $codes;
}

function get_user($mail = null) {
	$mail or $mail = $_SESSION['2fa']['mail'];

	$db = get_db();
	if ( $user = @$db['users'][$mail] ) {
		return compact('mail') + $user;
	}
}

function get_db() {
	if ( !file_exists(PROJECT_DB_FILE) || !is_writable(PROJECT_DB_FILE) ) {
		throw new Exception(PROJECT_DB_FILE . ' must be writable');
	}

	return @json_decode(file_get_contents(PROJECT_DB_FILE), true) ?: [];
}
