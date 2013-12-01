<?php
$time = time();
do {
	sendsms();
	sleep(3);
} while((time() - $time) < 55);

function sendsms() {
	$url = 'http://work.500mi.com:8000/timetask/task/SMSSEND01';
	$ch = curl_init();
	$timeout = 5;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
}
//EOF