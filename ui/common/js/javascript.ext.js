/**
 * 原生Javascript自定义扩展
 * 语法格式符合原生Javascript风格的，才可以放在这里...
 */

 /**
 * 字符串转对象
 * 与原生 toSource 类似
 */
String.prototype.toObject = function() {
	return eval('(' + this.valueOf() + ')');
	//alert(eval('(' + this.valueOf() + ')'));
};
