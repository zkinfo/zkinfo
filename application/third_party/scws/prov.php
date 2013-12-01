<?php
	$prov=array(
		'浙江省','浙江'
		);
	$buf = get_included_files();
	if (count($buf) == 1) {
	header('Content-Type:text/html;charset=utf-8;');
	echo '<div style="word-break:break-all;word-wrap:break-word ">';
	highlight_file(__FILE__);
	echo '</div>';
}
?>