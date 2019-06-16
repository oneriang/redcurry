<?php

require_once PathManager::getControllerDirectory() . '/api/base_controller.php';

class CustomValuesController extends BaseController
{
	//POSTメソッドでリクエストの場合
	public function post()
	{
		$params = $this -> request -> getRestParams();
		
		$svc = $this -> service('CustomValuesService');
		$res = $svc -> post($params);
		var_dump($res);
	}

	//PUTメソッドでリクエストの場合
	public function put()
	{
		$params = $this -> request -> getRestParams();

		$this -> values = $params;

		$this -> where = array('subject' => $params['subject']);

		parent::put();
	}

	//DELETEメソッドでリクエストの場合
	public function delete()
	{
		$params = $this -> request -> getRestParams();

		$this -> where = array('subject' => $params['subject']);

		parent::delete();
	}



}
