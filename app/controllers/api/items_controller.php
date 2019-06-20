<?php

require_once PathManager::getControllerDirectory() . '/api/base_controller.php';

class ItemsController extends BaseController
{
	//POSTメソッドでリクエストの場合
	public function post()
	{
		$params = $this -> request -> getRestParams();

		if ($params['data']['Method'] == 'PUT')
		{
			$svc = $this -> service('ItemsService');
			$res = $svc -> put($params);
			var_dump($res);
			return;
		}
		else
		if ($params['data']['Method'] == 'DELETE')
		{
			$this -> delete();
			return;
		}

		$svc = $this -> service('ItemsService');
		$res = $svc -> post($params);
		var_dump($res);
	}
}
