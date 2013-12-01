<?php
/**
 * 500mi iTV api libirary
 * 类目api
 * @author scorpio
 */
class Api_cate
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('item/category_biz');
	}

	// 获取类目列表(包括子类目)
	public function getCateList()
	{
		$category_list = $this->ci->category_biz->getChildCategoryList();
		foreach ($category_list as $key => $value) {
			
			$chd_cate_list = $this->ci->category_biz->getChildCategoryList($value['id']);
			$chd_cate_id_array = array();
			foreach ($chd_cate_list as $chd_key => $chd_cate) {
				$chd_cate_id_array[] = $chd_cate['id'];
			}

			$chd_cate_id_string = '';
			if (count($chd_cate_id_array) > 0) $chd_cate_id_string = implode(',', $chd_cate_id_array);
			
			$category_list[$key]['chd_cate'] = $chd_cate_list;
		}
		return $category_list;
	}
}