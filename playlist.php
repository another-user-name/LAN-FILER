<?php
include_once 'fileOperation.php';
include_once 'util.php';
include_once 'commonElement.php';

class PlayList {
	private $ilist = array();
	private $list_id = '';
	private $user_id = '';
	private $needWriteBack = false;
	private $filename = '';
	private $deleted = false;
	
	public function __construct($list) {
		$this->filename = PLAYLIST_DIR . $list;
		if (file_exists($this->filename) == false) {
			file_put_contents($this->filename, '');
		}
		$data = file_get_contents($this->filename);
		if ($data && $data != '') {
            $this->list_id = $list;
            $this->decodeMyDta($data);
		}
	}

	private function decodeJsonData($data) {
        $nThis = json_decode($data);
        foreach ($nThis as $key => $value) {
            $this->ilist[toUTF8($key)] = localConv($value);
        }
        $this->deleted = false;
        $this->needWriteBack = false;
    }

    private function decodeMyDta($data) {
        $datas = explode("\n", $data);
        foreach ($datas as $key => $value) {
            $files = explode(":::", $value);
            if (count($files) == 2) {
                $this->ilist[toUTF8($files[0])] = $files[1];
            }
        }
    }

	public function __destruct() {
		if ($this->needWriteBack && $this->deleted == false) {
		} else if ($this->deleted == true) {
			file_delete($this->filename);
		}
	}
	
	public function __toString() {
		return $this->list_id;
	}
	
	public function writeback() {
		if ($this->needWriteBack && $this->deleted == false) {
            $data = $this->outputMyData();
			if (file_put_contents($this->filename, $data)) {
				$this->needWriteBack = false;
			}
		}
	}

	private function outputMyData() {
        $data = "";
        foreach ($this->ilist as $key => $value) {
            $data .= $key . ":::" . $value . "\n";
        }
        return $data;
    }
	
	public function renameFile($old, $new) {
		if (isset($this->ilist[$old]) && $old != $new) {
			$this->ilist[$new] = $this->ilist[$old];
			unset($this->ilist[$old]);
			$this->changed();
		}
	}

	public function getFilePath($filename) {
		if (isset($this->ilist[$filename])) {
			return $this->ilist[$filename];
		} else {
			return '';
		}
	}

	public function containsFile($filename) {
		//$filename = substr($filename, strrpos($filename, '/') + 1);
		return isset($this->ilist[toUTF8($filename)]);
	}
	
	public function addFolder($folder) {
		$formats = array('mp3' => 1, 'mp4' => 2);
		$files = get_files_by_type($folder, $formats);
		if ($files && count($files) > 0) {
			$this->changed();
			foreach ($files as $key => $value) {
				$this->ilist[$key] = $value;
			}
		}
	}
	
	public function addList($list_id) {
		$other = new PlayList($list_id);
		$lists = $other->getList2();
		if ($lists && count($lists) > 0) {
			$this->changed();
			foreach ($lists as $key => $value) {
				if (isset($this->ilist[$key]) == false) {
					$this->ilist[$key] = $value;
				}
			}
		}
	}
	
	public function addFile($file) {
		$file = get_local_path($file);
		if (file_exists($file)) {
			$this->changed();
			$filename = toUTF8(($file));
			$this->ilist[$filename] = $file;
			return true;
		} else {
			return false;
		}
	}
	
	public function cutFile($file) {
		$filename = toUTF8(($file));
		if (isset($this->ilist[$filename])) {
			$this->changed();
			unset($this->ilist[$filename]);
		}
	}
	
	public function cutList($list_id) {
		$other = new PlayList($list_id);
		$lists = $other->getList2();
		if ($lists && count($lists) > 0) {
			$this->changed();
			foreach ($lists as $key => $value) {
				if (isset($this->ilist[$key])) {// && $this->ilist[$key] == $value) {
					unset($this->ilist[$key]);
				}
			}
		}
	}
	
	public function cutFolder($folder) {
		$formats = array('mp3' => 1, 'mp4' => 2);
		$files = get_files_by_type($folder, $formats);
		if ($files && count($files) > 0) {
			$this->changed();
			foreach ($files as $key => $value) {
				if (isset($this->ilist[$key])) {// && $this->ilist[$key] == $value) {
					unset($this->ilist[$key]);
				}
			}
		}
	}
	
	public function deleteSelf() {
		$this->needWriteBack = false;
		$this->deleted = true;
	}

	public function deleteList($list_id) {
		$listFilename = self::$PlayListFolder . $list_id;
		file_delete($listFilename);
	}
	
	public function outputJson() {
		return json_encode($this->getList());
	}
	
	private function changed() {
		$this->needWriteBack = true;
	}

	public function getList() {
		$list = array();
		foreach ($this->ilist as $key => $value) {
			$list[toUTF8($key)] = toUTF8($value);
		}
		return $list;
	}
	
	private function getList2() {
		return $this->ilist;
	}
}


?>