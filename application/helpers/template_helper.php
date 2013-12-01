<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JETLEE
 * 与模板处理相关的辅助类
 * 
 *
 */	

function subtplcheck($subfiles, $mktime, $tpl,$tplrefresh=0) {
	
	if($tplrefresh&& ($tplrefresh == 1 || mt_rand(1, $tplrefresh) == 1)) {
		$subfiles = explode('|', $subfiles);
		foreach ($subfiles as $subfile) {
			$tplfile = $subfile.'.htm';
			@$submktime = filemtime($tplfile);
			if($submktime > $mktime) {
				include_once(APPPATH.'libraries/Template.php');
				parse_template($tpl);
				break;
			}
		}
	}
}

// 表单加密验证，防止重复提交表单与csrf
function formhash() {
	$CI =& get_instance();
	$mtime = explode(' ', microtime());
	$timestamp = $mtime[1];
	$uid = $Ci->session->userdata('uid');
	$salt = '[、Z$e2*/';

	$formhash = $CI->session->userdata('formhash');

	if(!empty($formhash)) {
		$formhash = substr(md5(substr($timestamp, 0, -7).'|'.$uid.'|'.md5($salt).'|'), 8, 8);
		$CI->session->set_userdata('formhash', $formhash);
	}
	return $formhash;
}

function debuginfo($starttime=0,$isshow=0) {
	if(!$isshow) {
		$info = '';
	} else {
		$mtime = explode(' ', microtime());
		$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 4);
		$info = 'Processed in '.$totaltime.' second(s)';
	}

	return $info;
}


if (!function_exists("json_encode")) {
   include_once("JSON.php");
 
   function json_encode($array) {
       $json = new Services_JSON();
       $json_array = $json->encode($array);
       return $json_array;
   }
 
   /**
    * ����JSON���
    * @param string $json_data ��Ҫ�����JSON���                                                      
    * @param bool $toarray �Ƿ���Ҫ���������                                      
    * @return array ���ؽ���������
    */
   function json_decode($json_data, $toarray = TRUE) {
       $json = new Services_JSON();
       $array = $json->decode($json_data);
 
       if ($toarray) { //  ��Ҫת��������
           $array = object2array($array);
       }
       return $array;
   }
   
   function object2array($object) {
   		$array = array();
		if (is_object($object) || is_array($object)) {
			foreach ($object as $key => $value) {
				//print ��$key\r\n��;
				$array[$key] = object2array($value);
			}
		}else {
			$array = $object;
		}
		return $array;
	}
	
	function array2object($arrGiven){
		//create empty class
		$objResult=new stdClass();
		
		foreach ($arrLinklist as $key => $value){
			//recursive call for multidimensional arrays
			if(is_array($value)) $value=array2object($value);
			
			$objResult->{$key}=$value;
		}
		return $objResult;
	}


}
if ( ! function_exists('resource')) {
	function resource($file='',$level='',$display='url',$params='') {
		$CI =& get_instance();
		$CI->load->library('assets');
		if($file === 'display_header_assets'){
			return $CI->assets->display_header_assets();
		}elseif($display === 'url'){
			return $CI->assets->url($file,$level);
		}elseif($display === 'file'){
			return $CI->assets->load($file,$level,$params);
		}else{
			return $CI->assets->url($file,$level);
		}	
	}
}

/**
 * layout_content 
 */
function mi_tpl($template) {
	$CI =& get_instance();
	$cnt = $CI->load->template($template,array(),true);
	return $cnt;
}

/**
 * Control 控件
 */
function control($name,$vars=array(),$flag='') {
	if ( ! class_exists('MY_Control')){
		$control = load_class('Control', 'core');
	}
	
	$module = CI::$APP->router->fetch_module();
	list($floder_path, $_control) = Modules::find($name.'_control', $module, 'controls/');
	$path = FCPATH.$floder_path.$name.'_control';
	if (file_exists($path.'.php')) {
		$_control = $name.'_control';
		//$_control = ucfirst($name.'_control');
		Modules::load_file($_control, FCPATH.$floder_path);
		$control = new $_control($vars);
		$control->set_file($_control);
		return $control->render($vars);
	} else {
		if (empty($vars)) {
			include_once(APPPATH.'libraries/Template.php');
			$template = new Template();
			$content = $template->sreadfile($path.'.htm');
		} else {
			$control->set_file($path);
			$content = $control->render($vars);
		}

		return $content;
	}
}

/**
 * Form 表单控件
 * @param $type 表单类型 select (text radio checkbox textarea 待实现)
 * @param $idclass 表单名字，name, id, class 规则: name[#id][.class.class2.class3]
 * @param $vars 表单数据 目前支持 const_xxx.field  快速查找解析
 * @param $is 是否选中， 一般传入get值
 */
function form($type, $idclass, $vars, $is='') {
	$html = '';
	preg_match('/^\w+/', $idclass, $matches);
	$name = $matches[0];
	preg_match('/#(\w+)/', $idclass, $matches);
	$id = $matches[1];
	preg_match_all('/\.(\w+)/', $idclass, $matches);
	$class = implode(' ', $matches[1]);
	if ( ! is_array($vars)) {
		if (strpos($vars,'const') !== false) {
			$vars_row = explode('.', $vars);
			$CI =& get_instance();
			$CI->load->conster($vars_row[0], true);
			$vars = $CI->conster->item($vars_row[1], $vars_row[0]);
		}
	}
	switch ($type) {
		case 'select':
			$html .= '<select name="'.$name.'"'.' id="'.$id.'" class="'.$class.'">'."\n";
			$html .= '<option value="">'.$vars['name']."</option>\n";
			foreach ($vars as $i => $var) {
				if ($i === 'name' || $i === 'rule') continue;

				if ($vars['rule'] == '&') {
					if ($i & $is) {
						$html .= '<option value="'.$i.'" selected>'.$var."</option>\n";
					} else {
						$html .= '<option value="'.$i.'">'.$var."</option>\n";
					}
				} else if ($vars['rule'] == '=') {
					if ($i == $is && strlen($i) === strlen($is)) {
						$html .= '<option value="'.$i.'" selected>'.$var."</option>\n";
					} else {
						$html .= '<option value="'.$i.'">'.$var."</option>\n";
					}
				} else {
					$html .= '<option value="'.$i.'">'.$var."</option>\n";
				}				
			}
			$html .= "</select>\n";
			break;
		default:
			//;
	}
	return $html;
}

/**
 * 购买按钮的扩展相信
 * @param $item 
 */
function wk_item_info($item) {
	$btn_ext = ' data-item-id="'.$item['id'].'"';
	$btn_ext .= 'data-sin="'.$item['sin'].'"';
	$btn_ext .= 'data-attribute="'.$item['attribute'].'"';
	$btn_ext .= 'data-item-name="'.$item['item_name'].'"';
	$btn_ext .= 'data-item-code="'.$item['item_code'].'"';
	$btn_ext .= 'data-spu-id="'.$item['spu_id'].'"';
	$btn_ext .= 'data-category-id="'.$item['category_id'].'"';
	$btn_ext .= 'data-item-pic="'.$item['image_value'].'!80x80"';
	$btn_ext .= 'data-spot-price="'.$item['spot_price_value'].'"';
	$btn_ext .= 'data-spot-price-real="'.$item['spot_price_real_value'].'"';
	$btn_ext .= 'data-spec="'.$item['spec_value'].'"';
	$btn_ext .= 'data-partner-id="'.$item['partner_id'].'"';
	$btn_ext .= 'data-discount-type="'.$item['discount_type'].'"';
	$btn_ext .= 'data-total="'.$item['spot_rule']['total'].'"';
	$btn_ext .= 'data-minimum="'.$item['spot_rule']['minimum'].'"';
	$btn_ext .= 'data-maxinum="'.$item['spot_rule']['maxinum'].'"';
	$btn_ext .= 'data-start-time="'.$item['spot_rule']['start_time'].'"';
	$btn_ext .= 'data-end-time="'.$item['spot_rule']['end_time'].'" ';

	return $btn_ext;
}

function mi_money($money) {
	return sprintf('%.2f', $money);
}
?>
