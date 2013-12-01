<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
/*******************************************************************************
 *
 * 类  名 : 五百米 (采集机器人 )
 * 功　能 : 产品信息采集
 * 操作表 : tt_location
 *
*******************************************************************************/
require_once(APPPATH.'./libraries/other/robot_base.php');

class Product_Robot extends Robot_Base
{
	private $url = 'http://search.anccnet.com/searchResult.aspx?keyword=';
	private $html;

	private $matches;

	private $brand;
	private $product;
	private $sin;
	private $spec;

	private $id;

	private $row;
	private $data;
	private $spu;

	private $isRobot = 0;

	public function __construct()
	{
		parent::__construct();
		$this->ci->load->business('item/spu_biz');
	}

	public function autoGet($product_code)
	{
		$this->isRobot += 1;
		$this->sin = $product_code;
		$this->url .= $product_code;
		$this->getProductInfo();
		//回调
		return $this->ci->spu_biz->getSpu( array('id' => $this->id));		
	}

	public function get($product_code)
	{
		$isSpu = $this->ci->spu_biz->getSpuBySin($product_code);
		if ( empty($isSpu) && $this->isRobot === 0 ) {
			$this->isRobot += 1;
			$this->sin = $product_code;
			$this->url .= $product_code;
			$this->getProductInfo();
			//回调
			return $this->ci->spu_biz->getSpuById($this->id);		
		} else {
			$this->isRobot = 0;
			return $isSpu;
		}
	}

	private function getProductInfo()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Product');
		preg_match_all('/<dd>\s*(<a\s+.*>)*([^<]+)[(<\/a>)]*\s*<\/dd>/', $this->html, $matches);
		//var_dump($matches); //die;

		if (is_array($matches) && isset($matches[2][2]) ) {
			$matches = $matches[2];		
			$this->ci->logger->log(6, '解析成功:'.count($matches[2]), 'Robot-Product');
			$this->matches 	= $matches;
			$this->spec 	= $matches[3];
			$this->html 	= '';
			$this->pushData();
			$this->saveProduct();
		} else {			
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr(json_encode($matches),0,80), 'Robot-Product');
			//die('解析失败：摘要 => '.substr(json_encode($matches),0,80));
		}
	}

	/**
	 * 采集入口
	 * url => http://www.stats.gov.cn/tjbz/cxfldm/2011/index.html
	 */
	public function run()
	{
		$this->url = 'http://search.anccnet.com/searchResult.aspx?keyword=6921168504015';
		$this->ci->logger->log(6, '采集产品信息 任务开始：', 'Robot-Product');
		$this->ci->logger->log(6, '采集入口：'.$this->url, 'Robot-Product');
		$this->getProvinceList();
	}

	/**
	 * 抓取页面HTML
	 */
	private function getHtml()
	{		
		$this->ci->logger->log(6, '正在抓取页面：'.$this->url, 'Robot-Product');
		$html = file_get_contents($this->url);
		$html = iconv('','utf-8',$html);
		if ($html) {
			$this->ci->logger->log(6, '抓取成功：'.strlen($html).'字节', 'Robot-Product');
			$this->html = $html;
		} else {
			$this->ci->logger->log(6, '抓取失败：'.$this->url, 'Robot-Product');
			sleep(1);
			$this->ci->logger->log(6, '重新抓取：'.$this->url, 'Robot-Product');
			$this->getHtml();
			//die('抓取失败：'.$this->url);
		}
	}

	/**
	 * 组装一条数据
	 */
	private function pushData()
	{
		//产品字段
		preg_match('/^([0-9]+)([^0-9]+)$/', $this->spec, $spec);
		//var_dump($spec); die;
		$this->ci->load->conster('const_spu');

		$this->data[] = $this->row = $this->spu = array(
			'category_id' 	=> $this->ci->conster->item('spot_category_id'),
			'spu_name' 		=> $this->matches[2],
			'sin' 			=> $this->sin,
			'spec' 			=> $spec[1].$spec[2],
			'description' 	=> trim($this->matches[4], ' ,，'),
			'issku' 		=> 1,
			'source' 		=> 2,
			'tag' 			=> $this->matches[0]
		);
		//var_dump($this->data); die;
		$this->ci->logger->log(6, '组装数据:'.implode("\t", $this->row), 'Robot-Product');
	}

	private function saveProduct()
	{
		$this->ci->logger->log(6, '预备保存数据:'.$this->product, 'Robot-Product');
		//print_r($this);die;
		//require_once(APPPATH.'./models/spu_model.php');
		//$spu_model = new Spu_model;
		if ($this->id = $this->ci->spu_biz->addSpu($this->spu)) {
			$this->ci->logger->log(6, '存储数据成功:'.$this->product, 'Robot-Product');
		} else {
			$this->ci->logger->log(6, '存储数据失败:'.$this->product, 'Robot-Product');
			die('存储数据失败:'.$this->product);
		}
		//$this->freeData();
	}

	/**
	 * 保存数据
	 */
	private function saveData()
	{
		$this->ci->logger->log(6, '预备保存数据, 数量:'.count($this->data), 'Robot-Product');
		if (count($this->data) >= 100) {
			$this->ci->logger->log(6, '批量单位完成, 存储完成后释放, 当前数量:'.count($this->data), 'Robot-Product');
			if ($this->ci->spu_biz->insert_batch($this->data)) {
				$this->ci->logger->log(6, '存储数据成功, 数量:'.count($this->data), 'Robot-Product');
			} else {
				$this->ci->logger->log(6, '存储数据失败, 数量:'.count($this->data), 'Robot-Product');
				die('存储数据失败, 数量:'.count($this->data));
			}
			$this->freeData();
		}
	}

	/**
	 * 释放值域
	 */
	private function freeData()
	{			
		//释放数据
		/*
		$this->village 	= '';
		$this->areacode = '';
		$this->type 	= '';
		$this->province = '';
		$thisty 	= '';
		$this->district = '';
		$this->town 	= '';
		$this->village 	= '';
		*/
		$this->data 	= array();
	}

} //Class:EOF