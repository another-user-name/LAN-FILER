function searchTable(key) {
	var arr = new Array();
	$("#file_list tr").each(function(index) {
		if (strpos($(this).html(), key) >= 0) {
			arr.push(index);
		}
	});
	return arr;
}

arr = new Array();
now = 0;
prevkey = '';

function search(key) {
	now = 0;
	arr = searchTable(key);
}

function next(key) {
	if (key != prevkey) {
		prevkey = key;
		search(key);
	}
	if (now < arr.length) {
		$("html,body").animate({scrollTop:$("#file_list tr:eq(" + arr[now] +")").offset().top}, 500);
		now = now + 1;
	}
	// var myvar = $("table:first tr:eq(" + test +")").html();
	// $("html,body").animate({scrollTop:$("#file_list tr:eq(" + test +")").offset().top}, 500);
	// test = test + 3;
}

function prev(key) {

	if (key != prevkey) {
		prevkey = key;
		search(key);
	}
	if (now < arr.length && now >= 0) {
		$("html,body").animate({scrollTop:$("#file_list tr:eq(" + arr[now] +")").offset().top}, 500);
		now = now - 1;
	}
}
