<?php
function file_get_content($url,$post_filed) {
	$ch = curl_init();
	$timeout = 30;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_filed);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}
$return=file_get_content('http://117.121.25.192/sdc/curltest.php','');
die($return));
?>