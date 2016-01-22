<?php
class Calculation{
	public $cid;
	private $pid;
	private $uid;
	private $priority;
	private $public;
	private $password;
	private $status;
	private $input;
	private $result;

	public function init($pid, $uid, $priority, $public, $password, $status, $input) {
		$this->pid = $pid;
		$this->uid = $uid;
		$this->priority = $priority;
		$this->public = $public;
		$this->password = $password;
		$this->status = $status;
		$this->input = $input;
	}
	public function checkVariables() {
		if(!preg_match('/^[0-9]+$/', $this->pid)) return false;
		if(!preg_match('/^[0-9]+$/', $this->uid)) return false;
		if(!preg_match('/^[0-9]+$/', $this->priority)) return false;
		if(!preg_match('/^[0-1]+$/', $this->public)) return false;
		if(!preg_match('/^[0-9a-zA-Z]$/', $this->password)) return false;
		if(!preg_match('/^[0-5]+$/', $this->status)) return false;
	}
	public function getData(){
		if(($sqlCalculation = @mysql_query(
			'SELECT `cid`, `pid`, `uid`, `priority`, `public`, `password`, `status`,`input`, `result`
			FROM `calculation`
			WHERE `cid` = "'.$this->cid.'";')) === false) return false;
		if(($response = @mysql_fetch_assoc($sqlCalculation)) === false) return false;
		$this->pid = (int)$response['pid'];
		$this->uid = (int)$response['uid'];
		$this->priority = (int)$response['priority'];
		$this->public = (int)$response['public'];
		$this->password = $response['password'];
		$this->status = (int)$response['status'];
		$this->input = urldecode($response['input']);
		$this->result = urldecode($response['result']);
		return json_encode($response);
	}
	public function listData($uid=""){
		if(($sqlCalculation = @mysql_query(
			'SELECT `cid`, `pid`, `uid`, `priority`, `public`, `password`, `status`, `input`, `result`
			FROM `calculation`
			WHERE `uid` LIKE "%'.$this->uid.'%";')) === false) return false;
		$response = [];
		while(($item = @mysql_fetch_assoc($sqlCalculation)) !== false) {
			$item['cid'] = (int)$item['cid'];
			$item['pid'] = (int)$item['pid'];
			$item['uid'] = (int)$item['uid'];
			$item['priority'] = (int)$item['priority'];
			$item['public'] = (int)$item['public'];
			$item['status'] = (int)$item['status'];
			$item['input'] = urldecode($item['input']);
			$item['result'] = urldecode($item['result']);
			array_push($response, $item);
		}
		return json_encode($response);
	}
	public function create(){
		if(($sqlCalculation = @mysql_query(
			'INSERT INTO `calculation`
				(`pid`, `uid`, `priority`, `public`, `password`, `status`, `input`)
			VALUES (
				"'.$this->pid.'",
				"'.$this->uid.'",
				"'.$this->priority.'",
				"'.$this->public.'",
				"'.$this->password.'",
				"'.$this->status.'",
				"'.urlencode($this->input).'"
			);')) === false) return false;
		return (int)mysql_insert_id();
	}
	public function modify(){
		if(($sqlCalculation = @mysql_query(
			'UPDATE `calculation`
			SET 
				`priority` = "'.$this->priority.'",
				`public` = "'.$this->public.'",
				`password` = "'.$this->password.'"
			WHERE `cid` = "'.$this->cid.'";')) === false) return false;
		return true;
	}
}