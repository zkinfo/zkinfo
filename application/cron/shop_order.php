<?php

$debug = 0;

$path = dirname(__FILE__) . '/logs/';
if ( ! is_dir($path)) {
	mkdir($path,0777,true);
}

#set_include_path('D:\danchex\gitroot\wubaimi\application\third_party');
date_default_timezone_set('Asia/Shanghai');

require_once 'Zend/Log.php';
/*
 * 连接
 */
try {
    $pdo = new PDO('mysql:host=60.190.240.74;dbname=w500midb_java', 'midev', 'dev@500mi');
    $pdo->query("set names utf8");
} catch (PDOException $e) {
	$logger = Zend_Log::factory(array(
	    array(
	        'writerName'   => 'Stream',
	        'writerParams' => array(
	            'stream'   => $path . 'runtime.log', 'a', true
	        )
	    )
	));
    $logger->err("Error: " . $e->getMessage());
    die("Error: " . $e->getMessage() . PHP_EOL);
}

// $pdo->beginTransaction();
// var_dump($pdo->getAvailableDrivers()); die;

//测试取数据
//$rows = $pdo->query("select * from cp_trade_order limit 10");
//foreach ($rows as $row) {
	//echo $row['iname'] . "\n";
//}

//5天前已发货订单自动签收

$path = dirname(__FILE__) . '/logs/trade/order/';
if ( ! is_dir($path)) {
	mkdir($path,0777,true);
}

$logger = Zend_Log::factory(array(
    array(
        'writerName'   => 'Stream',
        'writerParams' => array(
            'stream'   => $path . 'complete-'.date('Y-m-d').'.log', 'a', true
        )
    )
));

$sql = '
/*本月进货额*/
select a.spot_code, a.uid, b.sum from cp_shop a left join (
select spot_code, user_id, round(sum(pay_real)/100,2) as sum 
from cp_trade_order 
where item_id > 0 and pay_status = 1 and left(cdate,7) = left(now(),7) 
group by spot_code, user_id, left(cdate,7)
order by sum desc ) b
on a.spot_code = b.spot_code
where a.spot_code is not null
order by sum asc
;
';
$sth = $pdo->query($sql);

while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$attr = 0;
	if ($row['sum'] == 0) {
		$attr = 1;
	} elseif ($row['sum'] > 0 && $row['sum'] < 2000) {
		$attr = 2;
	} elseif ($row['sum'] >= 2000 && $row['sum'] < 4000) {
		$attr = 4;
	} elseif ($row['sum'] >= 4000 && $row['sum'] < 8000) {
		$attr = 8;
	} elseif ($row['sum'] >= 8000) {
		$attr = 16;
	}
	$_sql = 'update cp_shop set active = (active & 0) | '.$attr.' where spot_code = ?';
	$_sth = $pdo->prepare($_sql);
	$_sth->execute(array($row['spot_code']));
}

$cdate = date('Y-m-01 00:00:00', strtotime('-1 month'));
$sql = <<<EOT
/*两个自然月内进货额 用于判定是否新店*/
select a.spot_code, a.uid, b.sum, b.count from cp_shop a left join (
select left(cdate,7), spot_code, user_id, round(sum(pay_real)/100,2) as sum, 
count(distinct date(cdate)) as count
from cp_trade_order 
where item_id > 0 and pay_status = 1 and cdate >= '$cdate'
group by spot_code, user_id, left(cdate,7)
order by sum desc ) b
on a.spot_code = b.spot_code
where a.spot_code is not null
order by sum desc
;
EOT;
$sth = $pdo->query($sql);

while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	if ($row['count'] > 3) {
		$_sql = 'update cp_shop set attribute = ((attribute | 32) ^ 32) where spot_code = ?';
	} else {
		$_sql = 'update cp_shop set attribute = (attribute | 32) where spot_code = ?';
	}
	$_sth = $pdo->prepare($_sql);
	$_sth->execute(array($row['spot_code']));
}

$logger = null;
$pdo = null;
?> 