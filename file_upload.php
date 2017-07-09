<?php
echo '<!DOCTYPE html>
<html>
  <head>
    <title>文件上传</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  </head>
  <body>
    <div class="container" style="text-align: center; margin: auto;">
      <div class="row">
        <div class="col-md-12">
          <div class="container" style="margin: auto; align-content: center; text-align: center; background: #00ffff;">
            <h1>
';
if (isset($_POST["submit"])) {
	if ($_FILES["file"]["error"] > 0) {
		echo "              Error :" . $_FILES["file"]["error"] . "<br />\n";
	} else {
		$name = iconv("UTF-8", "gb2312", $_FILES["file"]["name"]);
		echo "              Upload: " . $_FILES["file"]["name"] . "<br />\n";
		echo "              Type: " . $_FILES["file"]["type"] . "<br />\n";
		echo "              Size: " . ($_FILES["file"]["size"] / 1024) . " KiB<br />\n";
		echo "              Stored in: " . $_FILES["file"]["tmp_name"] . "<br />";
		move_uploaded_file($_FILES["file"]["tmp_name"], "./upload/" . $name);
		echo "              File are saved as: " . "upload/" . $_FILES["file"]["name"];
	}
	
}
echo "
            </h1>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>";
?>