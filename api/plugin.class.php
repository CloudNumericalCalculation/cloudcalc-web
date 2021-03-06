<?php
class Plugin{
	public $pid;
	private $uid;
	private $folder;
	private $cover;
	private $name;
	private $author;
	private $git;
	private $gitStatus;
	private $available;

	public function init($uid, $folder, $cover, $name, $author, $git, $gitStatus, $available) {
		$this->uid = $uid;
		$this->folder = $folder;
		$this->cover = $cover;
		$this->name = $name;
		$this->author = $author;
		$this->git = $git;
		$this->gitStatus = $gitStatus;
		$this->available = $available;
	}
	public function checkVariables() {
		if(!preg_match('/^[0-9]+$/', $this->uid)) return false;
		return true;
	}
	private function getFileInformation() {
		$path = '../plugin/'.$this->uid.'/'.$this->folder.'/';
		if(file_exists($path.'README.md')) {
			$data['readme'] = file_get_contents($path.'README.md');
		}
		else {
			$data['readme'] = '###该插件目前不可用！';
		}
		if(file_exists($path.'input.json')) {
			$data['input'] = json_decode(file_get_contents($path.'input.json'), true);
		}
		else {
			$data['input'] = [];
		}
		for($_i = 0; $_i < count($data['input']); $_i++) {
			$data['input'][$_i]['value'] = '';
		}
		return $data;
	}
	public function getData(){
		if(($sqlPlugin = @mysql_query(
			'SELECT `pid`, `uid`, `folder`, `cover`, `name`, `author`, `git`, `gitStatus`, `available`
			FROM `plugin`
			WHERE `pid` = "'.$this->pid.'";')) === false) return false;
		if(($response = @mysql_fetch_assoc($sqlPlugin)) === false) return false;
		$response['pid'] = (int)$response['pid'];
		$this->uid = $response['uid']= (int)$response['uid'];
		$this->folder = $response['folder'] = urldecode($response['folder']);
		$this->cover = $response['cover'] = urldecode($response['cover']);
		$this->name = $response['name'] = urldecode($response['name']);
		$this->author = $response['author'] = urldecode($response['author']);
		$this->git = $response['git'] = urldecode($response['git']);
		$this->gitStatus = $response['gitStatus'] = (int)$response['gitStatus'];
		$this->available = $response['available'] = (bool)$response['available'];
		if(!$response['available'] && !checkAuthority(9)) return false;
		$response['file'] = $this->getFileInformation();
		return json_encode($response);
	}
	public function listData($uid=""){
		if(($sqlPlugin = @mysql_query(
			'SELECT `pid`, `uid`, `folder`, `cover`, `name`, `author`, `git`, `gitStatus`, `available`
			FROM `plugin`
			WHERE `uid` LIKE "%'.$uid.'%";')) === false) return false;
		$response = [];
		while(($item = @mysql_fetch_assoc($sqlPlugin)) !== false) {
			$item['pid'] = (int)$item['pid'];
			$item['uid'] = (int)$item['uid'];
			$item['folder'] = urldecode($item['folder']);
			$item['cover'] = urldecode($item['cover']);
			$item['name'] = urldecode($item['name']);
			$item['author'] = urldecode($item['author']);
			$item['git'] = urldecode($item['git']);
			$item['gitStatus'] = (int)$item['gitStatus'];
			$item['available'] = (bool)$item['available'];
			if(!$item['available'] && !checkAuthority(9)) continue;
			array_push($response, $item);
		}
		return json_encode($response);
	}
	public function create(){
		if(($sqlPlugin = @mysql_query(
			'INSERT INTO `plugin`
				(`uid`, `folder`, `cover`, `name`, `author`, `git`)
			VALUES (
				"'.$this->uid.'",
				"'.urlencode($this->folder).'",
				"'.urlencode($this->cover).'",
				"'.urlencode($this->name).'",
				"'.urlencode($this->author).'",
				"'.urlencode($this->git).'"
			);')) === false) return false;
		return (int)mysql_insert_id();
	}
	public function modify(){
		if(($sqlPlugin = @mysql_query(
			'UPDATE `plugin`
			SET 
				`cover` = "'.urlencode($this->cover).'",
				`name` = "'.urlencode($this->name).'",
				`author` = "'.urlencode($this->author).'",
				`gitStatus` = "'.$this->gitStatus.'",
				`git` = "'.urlencode($this->git).'",
				`available` = "'.$this->available.'"
			WHERE `pid` = "'.$this->pid.'";')) === false) return false;
		return true;
	}
}
