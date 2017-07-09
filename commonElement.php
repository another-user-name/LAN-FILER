<?php
function loginPage() {
	echo <<<border
<script type='text/javascript'>
	window.location.href = "login.php";
</script>
border;
}

function htmlStart($title = "") {
    echo <<<border
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/font-awesome.min.css">
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/mylib.js"></script>
    <title>{$title}</title>
  </head>
  <body>
border;
}

function myHeader() {
	echo <<<border
<div style="position:relative; height:auto; min-height:100%;">
	<div style="padding-bottom:60px;">
		<table class='table'>
		  <tr>
		    <td>
			<div style="margin:30px;margin-bottom:0px;">
				<a href="javascript:goback()">
					<h1>
						<i class="icon-arrow-left"></i>&nbsp;&nbsp;Go Back
					</h1>
				</a>
			</div>
			</td>
			<td align="right">
			<div style="margin:30px;margin-bottom:0px;">
				<a href="javascript:logout()">
					<h1>
						Log Out&nbsp;&nbsp;
					</h1>
				</a>
			</div>
			</td>
		  </tr>
		</table>
		<script type="text/javascript">
			function goback() {
				history.back();
			}
			function logout() {
				$.post("logout.php", {'action':'logout'}, function(data, status) {
					//var myjson = $.parseJSON(data);
					//if (myjson['status'] == 'success') {
						window.location.href = "login.php";
					//}
				});
			}
		</script>
border;
}

function myFooter() {
	echo <<<border
	</div>
	<div id="footer233" style="position:absolute; bottom:0; left:0; right:0; height:60px;">
		<div align='center' style="margin:auto;">
		<center>
			<p style="text-align:center; margin:auto;">Powered by PFH. Copyright© 0000-2333, PFH, All Rights Reserved.</p>
		</center>
		</div>
	</div>  
</div>
border;
}

function htmlEnd() {
    echo <<<border
  </body>
</html>
border;
}

function h_div($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("div", $class, $id, $style, $innerContent, $return);
}

function h_table($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("table", $class, $id, $style, $innerContent, $return);
}

function h_tr($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("tr", $class, $id, $style, $innerContent, $return);
}

function h_td($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("td", $class, $id, $style, $innerContent, $return);
}

function h_p($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("p", $class, $id, $style, $innerContent, $return);
}

function h_h1($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("h1", $class, $id, $style, $innerContent, $return);
}

function h_h2($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("h2", $class, $id, $style, $innerContent, $return);
}

function h_h3($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("h3", $class, $id, $style, $innerContent, $return);
}

function h_row($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("row", $class, $id, $style, $innerContent, $return);
}

function h_column($return=false, $innerContent="", $class="", $id="", $style="") {
    return createTag("column", $class, $id, $style, $innerContent, $return);
}

function createTag($tag, $class, $id, $style, $innerContent, $return) {
    if ($return) {
        return "<{$tag} class='{$class}' id='{$id}' style='{$style}'>{$innerContent}</{$tag}>";
    } else {
        echo "<{$tag} class='{$class}' id='{$id}' style='{$style}'>{$innerContent}</{$tag}>";
    }
}

function h_a($return=false, $innerContent="", $href="", $class="", $id="", $style="") {
    if ($return) {
        return "<a href=\"{$href}\" class='{$class}' id='{$id}' style='{$style}'>{$innerContent}</a>";
    } else {
        echo "<a href=\"{$href}\" class='{$class}' id='{$id}' style='{$style}'>{$innerContent}</a>";
    }
}

function javascript($return=true, $script="") {
    if ($return) {
        return "<script type='text/javascript'>{$script}</script>";
    } else {
        echo "<script type='text/javascript'>{$script}</script>";
    }
}

function createTagWithAttr($tag, $class, $id, $style, $innerContent, $return, $attr) {
    if ($return) {
        return "<{$tag} class='{$class}' id='{$id}' style='{$style}' {$attr}>{$innerContent}</{$tag}>";
    } else {
        echo "<{$tag} class='{$class}' id='{$id}' style='{$style}' {$attr}>{$innerContent}</{$tag}>";
    }
}

function h_table_wa($return=false, $innerContent="", $class="", $attr="", $id="", $style="") {
    return createTagWithAttr("table", $class, $id, $style, $innerContent, $return, $attr);
}

function h_button_wa($return=false, $innerContent="", $class="", $attr="", $id="", $style="") {
    return createTagWithAttr("button", $class, $id, $style, $innerContent, $return, $attr);
}
?>