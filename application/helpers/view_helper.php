<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 对话框
if ( ! function_exists('showmessage'))
{
	function showmessage($msgkey, $url_forward = '', $second = 3, $msg_type = 'info', $msg_title = '信息提示', $values=array()) {
		$CI =& get_instance();
	
		$output = ob_get_contents();
		if($output){
			ob_end_clean();
		}
		
		// 语言
		$CI->lang->load('show_message');
		$message = $CI->lang->line($msgkey);
		if($message) {
			$message = lang_replace($message, $values);
		} else {
			$message = $msgkey;
		}

		// 显示
		if(!$CI->input->is_ajax_request() && $url_forward && empty($second)) {
			redirect($url_forward);
			exit();	
		} else {
			if($CI->input->is_ajax_request()) {
				if($url_forward) {
					$message = "<a href=\"$url_forward\">$message</a><ajaxok>";
				}
				$message = "<a href=\"javascript:;\" onclick=\"javascript:JqueryDialog.Close();\" class=\"float_del\">X</a><div class=\"popupmenu_inner\"><span class='showmessagespan'>".$CI->lang->line('box_title')."</span> $message</div>";
				ob_start();				
				// if($CI->input->is_ajax_request()) {
				// 	$message = "<root><![CDATA[".trim($message)."]]></root>";
				// }
				echo $message;
				exit();
			} else {
				$CI->load->library('Mi_common');
				$CI->load->library('layout', 'layouts/base/layout');

				$context = $CI->mi_common->create_app_and_menu();
				$context['msg_type']	= $msg_type;
				$context['msg_title']	= $msg_title;
				$context['message']		= $message;
				$context['second']		= $second;
				$context['url_forward']	= $url_forward;
				$context['cookie']		= $CI->session->userdata;

				$CI->layout->template('showmessage', $context);
			}
		}		
	}
}

function lang_replace($text, $vars) {
	if($vars) {
		foreach ($vars as $k => $v) {
			$rk = $k + 1;
			$text = str_replace('\\'.$rk, $v, $text);
		}
	}
	return $text;
}


function ckstart($start, $pagesize) {
	$maxpage = 100000;
	$maxstart = $pagesize*intval($maxpage);
	if($start < 0 || ($maxstart > 0 && $start >= $maxstart)) {
		showmessage('length_is_not_within_the_scope_of');
	}
}

function geturl($vars = array()) {
	if( empty($vars) ) $vars = $_GET;
	$url = '';
	$tmp = array();
	foreach($vars as $key => $var) {
		if (is_array($var)) {
			foreach ($var as $check) {
				$tmp[] = $key.'[]='.urlencode($check);
			}
		} else {
			$tmp[] = $key . '=' . urlencode($var);
		}
	}
	$url = implode('&', $tmp);

	return $url;
}

function multi($num, $pagesize, $curpage, $mpurl, $ajaxdiv='', $todiv='') {
	$CI = & get_instance();
	$maxpage = 100000;
	$showpage = 0;
	//$is_ajax = $CI->input->is_ajax_request();
	$is_ajax = FALSE;

	if(empty($ajaxdiv) && $is_ajax) {
		$ajaxdiv = $CI->input->get('ajaxdiv');
	}

	$page = 5;
	if($showpage) $page = $showpage;

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $pagesize) {
		$offset = 2;
		$realpages = @ceil($num / $pagesize);
		$pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = '';
		$urlplus = $todiv?"#$todiv":'';

		if($curpage > 1) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage-1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
			}
			$multipage .= " class=\"prev\">上一页</a>";
		}
		
		if($curpage - $offset > 1 && $pages > $page) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=1&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
			}
			$multipage .= " class=\"first\">1</a><span class=\"page-more\">···</span>";
		}
		
		for($i = $from; $i <= $to; $i++) {
			if($i == $curpage) {
				$multipage .= '<span class="current">'.$i.'</span>';
			} else {
				$multipage .= "<a ";
				if($is_ajax) {
					$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$i&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
				} else {
					$multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
				}
				$multipage .= ">$i</a>";
			}
		}

		if($to < $pages) {
			$multipage .= "<span class=\"page-more\">···</span><a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$pages&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
			}
			$multipage .= " class=\"last\">$realpages</a>";
		}

		if($curpage < $pages) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage+1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
			}
			$multipage .= " class=\"next\">下一页</a>";
		}
		if($multipage) {
			$multipage = '<div class="mi-pagination-inner"><span class="mi-pagination-count">共'.$num.'条</span>'.$multipage.'</div>';
		}
	}
	return $multipage;
}

function multi_items($num, $pagesize, $curpage, $mpurl, $ajaxdiv='', $todiv='') {
	$CI =& get_instance();
	$maxpage = 100000;
	$showpage = 0;
	//$is_ajax = $CI->input->is_ajax_request();
	$is_ajax = FALSE;

	if(empty($ajaxdiv) && $is_ajax) {
		$ajaxdiv = $CI->input->get('ajaxdiv');
	}

	$page = 5;
	if($showpage) $page = $showpage;

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') !== false ? '&' : '?';
	$realpages = 1;
	if($num > $pagesize) {
		$offset = 2;
		$realpages = @ceil($num / $pagesize);
		$pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = '';
		$urlplus = $todiv?"#$todiv":'';

		if($curpage > 1) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage-1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
			}
			$multipage .= " class=\"prev\">上一页</a>";
		}
		
		if($curpage - $offset > 1 && $pages > $page) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=1&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
			}
			$multipage .= " class=\"first\">1</a><span class=\"page-more\">···</span>";
		}
		
		for($i = $from; $i <= $to; $i++) {
			if($i == $curpage) {
				$multipage .= '<span class="current">'.$i.'</span>';
			} else {
				$multipage .= "<a ";
				if($is_ajax) {
					$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$i&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
				} else {
					$multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
				}
				$multipage .= ">$i</a>";
			}
		}

		if($to < $pages) {
			$multipage .= "<span class=\"page-more\">···</span><a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$pages&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
			}
			$multipage .= " class=\"last\">$realpages</a>";
		}

		if($curpage < $pages) {
			$multipage .= "<a ";
			if($is_ajax) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage+1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
			}
			$multipage .= " class=\"next\">下一页</a>";
		}

		if($multipage) {
			// $multipage = '共'.$num.'个商品'.$multipage;
			$multipage = '<div class="mi-pagination-inner"><span class="mi-pagination-count">共'.$num.'个商品</span>'.$multipage.'</div>';
		}
	}

	return $multipage;
}
?>
