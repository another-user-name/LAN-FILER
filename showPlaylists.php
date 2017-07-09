<?php
/**
 * Created by PhpStorm.
 * User: PFH
 * Date: 2016/11/24
 * Time: 17:21
 */
include_once 'MyHistory.php';
include_once 'playlist.php';
include_once 'util.php';
include_once 'commonElement.php';
session_start();

if (isset($_SESSION['user']) == false) {
    $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
    loginPage();
} else {
    if (isset($_REQUEST['action'])) {
        if ($_REQUEST['action'] == 'remove') {
            if (isset($_REQUEST['filename']) && isset($_SESSION['playlist'])) {
                $playlist_id = $_SESSION['playlist'];
                $filename = toUTF8($_REQUEST['filename']);
                $playlist = new PlayList($playlist_id);
                $playlist->cutFile($filename);
                $ret = array();
                $ret['list'] = $playlist->getList();
                echo json_encode($ret);
            }
        }
    } else if (isset($_REQUEST['playlist'])) {
        $playlist_id = $_REQUEST['playlist'];
        $_SESSION['playlist'] = $playlist_id;
        $playlist = new PlayList($playlist_id);
        $lists = $playlist->getList();


        htmlStart($playlist_id);
        myHeader();
        showAllPlaylists();

        $btn = h_button_wa(true, "New", "btn btn-default", "onClick='newList()'");
        $p = $btn;
        $btn = h_button_wa(true, "Delete", "btn btn-default", "onClick='deleteList()'");
        $p .= "&nbsp;&nbsp;&nbsp;" . $btn;
        $btn = h_button_wa(true, "Add List", "btn btn-default", "onClick='addList()'");
        $p .= "&nbsp;&nbsp;&nbsp;" . $btn;
        $btn = h_button_wa(true, "Minus List", "btn btn-default", "onClick='minusList()'");
        $p .= "&nbsp;&nbsp;&nbsp;" . $btn;
        $p = h_p(true, $p);
        h_div(false, $p, "container");

        $trs = "";
        $tr = "";
        $tds = "";
        $td = "";
        foreach ($lists as $key => $value) {
            $old = $key;
            $key = get_file_name($key);
            $td = h_a(true, $key, "play.php?filename=" . $value);
            $td = h_td(true, $td);
            $tr .= $td;
            $td = h_a(true, "Remove From List", "javascript:removeFromList('{$old}')");

            $td = h_td(true, $td);
            $tr .= $td;
            $tr = h_tr(true, $tr);
            $trs .= $tr;
            $tr = '';
        }
        $table = h_table_wa(true, $trs, "table table-condensed", "align='center'", "playlisttbl");
        h_div(false, $table, "container");

        echo <<<border
        <script type="text/javascript">
            function removeFromList(filename = '') {
                $.post("showPlaylists.php", {'action':'remove', 'filename':filename, 'playlist:':'{$playlist_id}'}, function(data1, status) {
                    var myjson = $.parseJSON(data1);
                    $("#add_cut_button").html(myjson['content']);
                    var list = myjson['list'];
                    mediaes = list;
                    $('#playlisttbl').html('');
                    $.each(list, function(key, value){
                        key = key.substring(key.lastIndexOf("/") + 1);
                        key = key.substring(key.lastIndexOf("\\\\") + 1);
                        $("<tr><td><a href='play.php?filename=" + value + "'>" + key + "</a></td><td><a href='javascript:addOrCut(\"" + value + "\")'>RemoveFromList</a></td></tr>").appendTo('#playlisttbl');
                    });
                });
            }
            function newList() {
                
            }
            function deleteList() {
                
            }
            function addList() {
                
            }
            function minusList() {
                
            }
            function getList() {
                
            }
        </script>
border;

        myFooter();
        htmlEnd();
    } else {
        htmlStart("playlist");
        myHeader();

        showAllPlaylists();

        myFooter();
        htmlEnd();
    }
}

function showAllPlaylists() {
    $playlists = get_files_by_type(PLAYLIST_DIR, array("lst"=>1));
    $trs = "";
    $tr = "";
    $tds = "";
    $td = "";
    $count = 0;
    foreach ($playlists as $value) {
        $list_id = toUTF8(get_file_name($value));
        $td = h_a(true, $list_id, "showPlaylists.php?playlist=" . $list_id);
        $td = h_td(true, $td, "col-md-3", "", "height:90px;");
        $tr .= $td;
        $count++;
        if ($count == 3) {
            $count = 0;
            $tr = h_td(true, "", "col-md-2", "", "height:90px;") . $tr;
            $tr = h_tr(true, $tr);
            $trs .= $tr;
            $tr = "";
        }
    }
    if ($tr != "") {
        $tr = h_td(true, "", "col-md-2", "", "height:90px;") . $tr;
        $tr = h_tr(true, $tr);
        $trs .= $tr;
        $tr = "";
    }

    $table = h_table(true, $trs, "table");
    h_div(false, $table, "table-responsive");
}