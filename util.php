<?php
include_once "const.php";
function toUTF8($str) {
	if (mb_check_encoding($str, "utf-8") == false) {
		return mb_convert_encoding($str, "utf-8", "ascii,gbk,gb2312,utf-8");
	} else {
		return $str;
	}
}

function toGBK($str) {
	if (mb_check_encoding($str, "gbk") == false) {
		return mb_convert_encoding($str, "gbk", "ascii,utf-8,gb2312,gbk");
	} else {
		return $str;
	}
}

function toGB2312($str) {
	if (mb_check_encoding($str, "gb2312") == false) {
		return mb_convert_encoding($str, "gb2312", "ascii,utf-8,gbk,gb2312");
	} else {
		return $str;
	}
}

function localConv($str) {
	//var_dump(strtoupper(substr(PHP_OS,0,3)));
	if (strtoupper(substr(PHP_OS,0,3))==='WIN') {
		//var_dump("GBK");
		return toGB2312($str);
	} else {
		//var_dump("UTF-8");
		return toUTF8($str);
	}
}

function localDiretorySeprator() {
	if (strtoupper(substr(PHP_OS,0,3))==='WIN') {
		//var_dump("GBK");
		return '/';
	} else {
		//var_dump("UTF-8");
		return '\\';
	}
}

?>