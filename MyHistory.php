<?php
class URLNode {
    public $prev = null;
    public $next = null;
    public $url = '';
    public function __construct($urlstr = '') {
        $this->prev = null;
        $this->next = null;
        $this->url = $urlstr;
    }
}

class MyHistory {
    private $head;
    private $now;
    
    public function __construct() {
        $this->head = new URLNode();
        $this->now = $this->head;
    }
    
    public function now() {
        return $this->now->url;
    }
    
    public function prev() {
        if ($this->now->prev == null) {
            return $this->now->url;
        } else {
            return $this->now->prev->url;
        }
    }
    
    public function next() {
        if ($this->now->next == null) {
            return $this->now->url;
        } else {
            return $this->now->next->url;
        }
    }
    
    public function visit($url) {
        if (isset($url)) {
            $this->now->next = new URLNode($url);
            $this->now = $this->now->next;
        }
    }
    
    public function back() {
        $tmp = $this->now;
        if ($this->now->prev != null) {
            $this->now = $this->now->prev;
        }
        return $tmp;
    }
    
    public function forward() {
        $tmp = $this->now;
        if ($this->now->next != null) {
            $this->now = $this->now->next;
        }
        return $tmp;
    }
}
?>