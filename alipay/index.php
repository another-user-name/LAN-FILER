<?php
function imagecropper($source_path)
{
	$source_info   = getimagesize($source_path);
	$source_width  = $source_info[0];
	$source_height = $source_info[1];
	$source_mime   = $source_info['mime']; 
 
 
	
	$oldimg = imagecreatefrompng($source_path);
	$base_width=350;
	$base_height=6;
	$baseimage = imagecreatetruecolor($base_width, $base_width);// 苹果图啊

	$thisimage = imagecreatetruecolor($base_width, $base_height); 
	     imagecopy($baseimage, $oldimg, 0, 0,       200,      642, $base_width, $base_height);  
	
	for ($i=0;$i<30;$i++){
		imagecopy($baseimage, $oldimg, 0, $i*($base_height*2)-$base_height, 200, (642+($i*12)),  $base_width, $base_height);  
		imagecopy($baseimage, $oldimg, 0, $i*($base_height*2), 200, (642+($i*12)),  $base_width, $base_height);  
	}
	


	header('Content-Type: image/png');
	imagepng($baseimage); 
	 
}
imagecropper("http://127.0.0.1/alipay/888.png")
?>
