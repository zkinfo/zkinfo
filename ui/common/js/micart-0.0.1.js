/*~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~
	Copyright (c) 2011-2013 Scorpio Zhou
	500mi.com
	VERSION 0.0.1
~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~*/
window._cartGuid = 1024;
var MiCart = {
	/**
	 * 初始化
	 * @param  {[type]} guid 购物车id
	 * @return {[type]}      [description]
	 */
	init : function(guid){
		if(!guid){
			guid = window._cartGuid;
		}
		if(!store.get('MiCart-'+guid)){
			store.set('MiCart-'+guid,JSON.stringify([]));
		}
		window._cartGuid = guid;
		return store.get('MiCart-'+guid);
	},
	/**
	 * 添加到购物车
	 * @param  {[json]} data [商品数据]
	 * @param  {[type]} n    [商品数量]
	 * @return {[type]}      [description]
	 */
	add : function(data, n){
		if(!n){
			n = 1;
		}
		var _predata = JSON.parse(store.get('MiCart-'+window._cartGuid)),
			_status = 0;
		// 如果之前存在该商品，直接加数量
		for(i in _predata){
			if(_predata[i].id == data.id){
				_predata[i]._num += n;
				_status ++;
			}
		}
		// 如果之前不存在该商品，直接添加商品
		if(!_status){
			data._num = n;
			_predata.push(data);
		}
		store.set('MiCart-'+window._cartGuid,JSON.stringify(_predata));
	},
	/**
	 * 从购物车中减少
	 * @param  {[type]} id 	 [商品id]
	 * @param  {[type]} n    [商品数量]
	 * @return {[type]}      [description]
	 */
	minus : function(id,n){
		if(!n){
			n = 1;
		}
		var _predata = JSON.parse(store.get('MiCart-'+window._cartGuid));
		for(i in _predata){
			if(_predata[i].id == id){
				if(_predata[i]._num <= n){
					// 如果要删除的数量超过了原有数量，直接删除该商品
					_predata.splice(i,1);
				}else{
					// 如果原有数量足够删除，直接减数量
					_predata[i]._num -= n;
				}
			}
		}
		store.set('MiCart-'+window._cartGuid,JSON.stringify(_predata));
	},
	/**
	 * 直接修改购物车数量
	 * @param  {[type]} id [description]
	 * @param  {[type]} n  [description]
	 * @return {[type]}    [description]
	 */
	change : function(id,n){
		var _predata = JSON.parse(store.get('MiCart-'+window._cartGuid));
		for(i in _predata){
			if(_predata[i].id == id){
				_predata[i]._num = n;
			}
		}
		store.set('MiCart-'+window._cartGuid,JSON.stringify(_predata));
	},
	/**
	 * 直接修改购物车的全部东西
	 * @return {[type]} [description]
	 */
	replace : function(data){
		store.set('MiCart-'+window._cartGuid,data);
	},
	/**
	 * 清空购物车
	 */
	clear : function(){
		store.set('MiCart-'+window._cartGuid,JSON.stringify([]));
	},
	/**
	 * 获取购物车统计
	 * @return {[type]} [description]
	 */
	total : function(){
		var _predata = JSON.parse(store.get('MiCart-'+window._cartGuid)),
			result = [];
		result.total = 0;
		result.num = 0;
		for(i in _predata){
			result.total = result.total + _predata[i]._num * _predata[i].price_value; 
			result.num = result.num + _predata[i]._num; 
		}
		return result;
	}
}