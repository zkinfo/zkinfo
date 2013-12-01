<?php
	$region=array(
		'浙江省','杭州市','拱墅区','西湖区','上城区','下城区','江干区','滨江区','余杭区','萧山区','建德市','富阳市','临安市','桐庐县','淳安县',
		'浙江','杭州','拱墅','西湖','上城','下城','江干','滨江','余杭','萧山','建德','富阳','临安','桐庐','淳安'
		);
	$buf = get_included_files();
	if (count($buf) == 1) {
	header('Content-Type:text/html;charset=utf-8;');
	echo '<div style="word-break:break-all;word-wrap:break-word ">';
	highlight_file(__FILE__);
	echo '</div>';
}
?>