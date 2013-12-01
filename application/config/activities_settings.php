<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * 活动配置
 *
**/

// 支付优惠，开启是1,关闭是0
$config['pay_discount'] = array(
							// 抵价券
							'ticket' 	=> array(
									'start_time'=> '2013-11-11 00:00:00',
									'end_time'	=> '2013-11-12 00:00:00',
									'status'	=> 0
								),
							// 红包
							'hongbao' 	=> array(
									'start_time'=> '2013-11-01 00:00:00',
									'end_time'	=> '2014-11-01 00:00:00',
									'status'	=> 1
								)
						);
// 红包使用规则,******阶梯顺序一定要正确,******
$config['hongbao_use_rule'] = array(
							// 支付满1000可用5元
							array(
								'pay' 	=> 100000,
								'limit' => 500
							),
							// 支付满2000可用15元
							array(
								'pay' 	=> 200000,
								'limit' => 1500
							),
							// 支付满3000可用25元
							array(
								'pay' 	=> 300000,
								'limit' => 2500
							)
						);
// 进货发抵价券
$config['stock_send_ticket'] = array(
							'start_time'=> '2013-11-10 00:00:00',
							'end_time'	=> '2013-11-11 00:00:00',
							'status'	=> 0
						);
// 进货发抵价券规则,******阶梯顺序一定要正确,******
$config['stock_send_ticket_rule'] = array(
							// 下单付款成功满1000可获得10元
							array(
								'pay' 	=> 100000,
								'limit' => 1000
							),
							// 下单付款成功满2000可获得20元
							array(
								'pay' 	=> 200000,
								'limit' => 2000
							),
							// 下单付款成功满3000可获得30元
							array(
								'pay' 	=> 300000,
								'limit' => 3000
							)
						);

?>
