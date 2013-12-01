<?php
/**
 * 500mi iTV api libirary
 * 商品api
 * @author scorpio
 */
class Api_item
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('item/category_biz');
		$this->ci->load->business('item/item_biz');
	}

	// 根据类目id获得商品列表
	public function getItemList()
	{
		// $area			= $this->session->userdata('area');
		$area			= "1";
		$search_key		= $this->ci->input->post('key', '');
		$search_type	= $this->ci->input->post('type', 'item');
		$search_cate	= $this->ci->input->post('cate');
		$search_offset	= $this->ci->input->post('offset');
		
		if (!$search_cate OR !check_int($search_cate) AND $search_cate != 'shop_item') {
			$search_cate = 'all';
		}
		if (!$search_offset OR !check_int($search_offset))	$search_offset = 0;

		$limit = 20;
		$param = array();
		$param['(area & '.$area.')'] = $area;
		$param['type in (0, 1)'] = NULL;
		$param['(attribute & (512 | 2048)) = 2048'] = NULL;
		
		if ($search_cate AND $search_cate != 'item') {
			$this->ci->load->business('item/category_biz');
			$category = $this->ci->category_biz->getCategoryById($search_cate);
			if ($category['status'] != 0) {
				$category = array();
			}
			$cateList = array();
			if ($category AND $category['parent_id'] == 0) {
				$cateIn = array();
				$cateList = $this->ci->category_biz->getChildCategoryList($search_cate);
				foreach ($cateList as $key => $value) {
					$cateIn[$key] = $value['id'];
				}
				$cateStr = implode(',', $cateIn);
				$param['category_id in ('.$cateStr.')'] = null;
			} elseif ($category AND $category['parent_id'] > 0) {
				$cateList = $this->ci->category_biz->getChildCategoryList($category['parent_id']);
				$param['category_id'] = $search_cate;
			}
			if (!empty($cateList))
			$this->_data['subcate_list'] = $cateList;
		}

		// 商品列表
		$item_list = $this->ci->item_biz->getItemViewList($param, '*', $search_offset, $limit, 'rank', $search_key);

		$this->_data['search']		= array('key' => $search_key, 'type' => $search_type, 'cate' => $search_cate);
		$this->_data['item_list']	= $item_list;
		return $item_list;
	}

	// 根据item_code获得商品
	public function getItem($item_code){
		$item = $this->ci->item_biz->getItemListByItemCodes($item_code);
		return $item;
	}
}