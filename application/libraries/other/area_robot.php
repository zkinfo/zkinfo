<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
/*******************************************************************************
 *
 * 类  名 : 五百米 (采集机器人 )
 * 功　能 : 行政区划采集
 * 操作表 : tt_location
 *
*******************************************************************************/
require_once(APPPATH.'./libraries/other/robot_base.php');

class Area_Robot extends Robot_Base
{
	private $url;
	private $html;
	private $matches;
	private $province;
	private $city;
	private $district;
	private $town;
	private $village;

	private $locaion;
	private $areacode;
	private $level;
	private $type;

	private $maxcode;

	private $data;
	private $row;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 采集入口
	 * url => http://www.stats.gov.cn/tjbz/cxfldm/2011/index.html
	 */
	public function run($maxcode = 0)
	{
		$this->maxcode = $maxcode;
		$this->url = 'http://www.stats.gov.cn/tjbz/cxfldm/2011/index.html';
		$this->ci->logger->log(6, '采集行政区划 任务开始：', 'Robot-Area');
		$this->ci->logger->log(6, '采集入口：'.$this->url, 'Robot-Area');
		$this->getProvinceList();
	}

	/*
	public function getContinue($maxcode = 130922103244)
	{
		$this->url = 'http://www.stats.gov.cn/tjbz/cxfldm/2011/'.
						 substr($maxcode,0,2).'/'.
						 substr($maxcode,2,2).'/'.
						 substr($maxcode,4,2).'/'.
						 substr($maxcode,0,9).'.html';
		$this->getVillageList();
	}
	/*

	/**
	 * 抓取页面HTML
	 */
	private function getHtml()
	{		
		$this->ci->logger->log(6, '正在抓取页面：'.$this->url, 'Robot-Area');
		$html = file_get_contents($this->url);
		$html = iconv('','utf-8',$html);
		if ($html) {
			$this->ci->logger->log(6, '抓取成功：'.strlen($html).'字节', 'Robot-Area');
			$this->html = $html;
		} else {
			$this->ci->logger->log(6, '抓取失败：'.$this->url, 'Robot-Area');
			sleep(1);
			$this->ci->logger->log(6, '重新抓取：'.$this->url, 'Robot-Area');
			$this->getHtml();
			//die('抓取失败：'.$this->url);
		}
	}

	/**
	 * 抓取省份页面
	 */
	private function getProvinceList()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Area');
		preg_match_all('/<a href=\'([0-9]{2}.html)\'>([^<]+)<\/a>/', $this->html, $matches);
		//var_dump($matches); die;
		if (is_array($matches)) {
			$this->ci->logger->log(6, '解析成功：匹配省份数'.count($matches[1]), 'Robot-Area');
			$current_url = substr($this->url, 0, strrpos($this->url, '/')+1);
			foreach($matches[1] as $i => $next_url) {
				$this->areacode = str_pad(substr($next_url, 0, strpos($next_url, '.')),12,'0',STR_PAD_RIGHT);
				$this->province = $this->location = $matches[2][$i];
				$this->level 	= 1;
				$this->url = $current_url . $next_url;
				$this->ci->logger->log(6, $this->url, 'Robot-Area');

				//断点继续
				if (substr($this->areacode,0,2) - substr($this->maxcode,0,2) < 0) continue;
					else if (substr($this->areacode,0,2) - substr($this->maxcode,0,2) > 0) $this->pushData();	//数据入组

				$this->getCityList();
			}
		} else {					
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr(json_encode($matches),0,80), 'Robot-Product');
			die('解析失败：摘要 => '.substr(json_encode($matches),0,80));
		}
	}

	/**
	 * 抓取城市页面
	 */
	private function getCityList()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Area');
		preg_match_all('/<tr class=\'citytr\'><td><a href=\'([0-9]{2}\/[0-9]{4}.html)\'>([0-9]{12})<\/a><\/td><td><a href=\'[0-9]{2}\/[0-9]{4}.html\'>([^<]+)<\/a><\/td><\/tr>/', $this->html, $matches);
		//var_dump($matches); die;
		if (is_array($matches)) {
			$this->ci->logger->log(6, '解析成功：匹配城市数'.count($matches[1]), 'Robot-Area');			
			$current_url = substr($this->url, 0, strrpos($this->url, '/')+1);
			foreach($matches[1] as $i => $next_url) {
				$this->areacode = $matches[2][$i];				
				$this->city 	= $this->location = $matches[3][$i];
				$this->level 	= 2;
				$this->url = $current_url . $next_url;
				$this->ci->logger->log(6, $this->url, 'Robot-Area');

				//断点继续
				if (substr($this->areacode,0,4) - substr($this->maxcode,0,4) < 0) continue;
					else if (substr($this->areacode,0,4) - substr($this->maxcode,0,4) > 0) $this->pushData();	//数据入组				

				$this->getDistrictList();
			}
		} else {			
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr($matches,0,80), 'Robot-Area');
			die('解析失败：摘要 => '.substr($matches,0,80));
		}	
	}

	/**
	 * 抓取县区页面
	 */
	private function getDistrictList()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Area');
		preg_match_all('/<tr class=\'countytr\'><td><a href=\'([0-9]{2}\/[0-9]{6}.html)\'>([0-9]{12})<\/a><\/td><td><a href=\'[0-9]{2}\/[0-9]{6}.html\'>([^<]+)<\/a><\/td><\/tr>/', $this->html, $matches);
		//var_dump($matches); die;
		if (is_array($matches)) {
			$this->ci->logger->log(6, '解析成功：匹配县区数'.count($matches[1]), 'Robot-Area');
			
			$current_url = substr($this->url, 0, strrpos($this->url, '/')+1);
			foreach($matches[1] as $i => $next_url) {
				$this->areacode = $matches[2][$i];
				$this->district = $this->location = $matches[3][$i];
				$this->level 	= 3;
				$this->url = $current_url . $next_url;
				$this->ci->logger->log(6, $this->url, 'Robot-Area');

				//断点继续
				if (substr($this->areacode,0,6) - substr($this->maxcode,0,6) < 0) continue;
					else if (substr($this->areacode,0,6) - substr($this->maxcode,0,6) > 0) $this->pushData();	//数据入组				

				
				$this->getTownList();
			}
		} else {			
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr($matches,0,80), 'Robot-Area');
			die('解析失败：摘要 => '.substr($matches,0,80));
		}	
	}

	/**
	 * 抓取(街道/镇)页面
	 */
	private function getTownList()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Area');
		preg_match_all('/<tr class=\'towntr\'><td><a href=\'([0-9]{2}\/[0-9]{9}.html)\'>([0-9]{12})<\/a><\/td><td><a href=\'[0-9]{2}\/[0-9]{9}.html\'>([^<]+)<\/a><\/td><\/tr>/', $this->html, $matches);
		//var_dump($matches); die;
		if (is_array($matches)) {
			$this->ci->logger->log(6, '解析成功：匹配(街道/镇)数'.count($matches[1]), 'Robot-Area');
			
			$current_url = substr($this->url, 0, strrpos($this->url, '/')+1);
			foreach($matches[1] as $i => $next_url) {
				$this->areacode = $matches[2][$i];
				$this->town 	= $this->location = $matches[3][$i];
				$this->level 	= 4;
				$this->url = $current_url . $next_url;
				$this->ci->logger->log(6, $this->url, 'Robot-Area');

				//断点继续
				if (substr($this->areacode,0,9) - substr($this->maxcode,0,9) < 0) continue;
					else if (substr($this->areacode,0,9) - substr($this->maxcode,0,9) > 0) $this->pushData();	//数据入组				

				$this->getVillageList();
			}
		} else {			
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr($matches,0,80), 'Robot-Area');
			die('解析失败：摘要 => '.substr($matches,0,80));
		}
	}

	/**
	 * 抓取(社区/村)页面
	 */
	private function getVillageList()
	{
		$this->getHtml();
		$this->ci->logger->log(6, '页面解析中：'.$this->url, 'Robot-Area');
		preg_match_all('/<tr class=\'villagetr\'><td>([0-9]{12})<\/td><td>([0-9]{3})<\/td><td>([^<]+)<\/td><\/tr>/', $this->html, $matches);
		//var_dump($matches); die;
		if (is_array($matches)) {
			$this->ci->logger->log(6, '解析成功：匹配(社区/村)数'.count($matches[1]), 'Robot-Area');
			
			foreach($matches[1] as $i => $next_url) {
				$this->areacode = $matches[1][$i];
				$this->level 	= 5;
				$this->type     = $matches[2][$i];
				$this->village  = $this->location = $matches[3][$i];

				//断点继续
				if ($this->areacode - $this->maxcode <= 0) continue;
					else $this->pushData();	//数据入组				

				//清洗数据
				$this->areacode = '';
				$this->type     = '';
				$this->village  = '';
			}
			//调用存储
			$this->saveData();
		} else {			
			$this->ci->logger->log(6, '解析失败：摘要 => '.substr($matches,0,80), 'Robot-Area');
			die('解析失败：摘要 => '.substr($matches,0,80));
		}
	}

	/**
	 * 组装一条数据
	 */
	private function pushData()
	{
		$this->data[] = $this->row = array(
			'lc_name' 	=> $this->location,
			'lc_code' 	=> $this->areacode,
			'lc_level' 	=> $this->level,
			'lc_type' 	=> $this->type,
			'province' 	=> $this->province,
			'city' 		=> $this->city,
			'district' 	=> $this->district,
			'street' 	=> $this->town,
			'community' => $this->village,
			'status' 	=> 1
		);
		//$this->ci->logger->log(6, '组装数据:'.implode("\t", $this->row), 'Robot-Area');
	}

	/**
	 * 保存数据
	 */
	private function saveData()
	{
		$this->ci->logger->log(6, '预备保存数据, 数量:'.count($this->data), 'Robot-Area');
		if (count($this->data) >= 100) {
			$this->ci->logger->log(6, '批量单位完成, 存储完成后释放, 当前数量:'.count($this->data), 'Robot-Area');
			$this->ci->load->model('location_model');
			if ($this->ci->location_model->insert_batch($this->data)) {
				$this->ci->logger->log(6, '存储数据成功, 数量:'.count($this->data), 'Robot-Area');
			} else {
				$this->ci->logger->log(6, '存储数据失败, 数量:'.count($this->data), 'Robot-Area');
				die('存储数据失败, 数量:'.count($this->data));
			}
			$this->freeData();
		} else if (count($this->data) > 1) {
			//var_dump($this->data); die;
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
		$this->city 	= '';
		$this->district = '';
		$this->town 	= '';
		$this->village 	= '';
		*/

		$this->data 	= array();
	}

} //Class:EOF