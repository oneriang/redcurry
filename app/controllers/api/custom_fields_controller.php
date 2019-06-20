<?php

require_once PathManager::getControllerDirectory() . '/api/base_controller.php';

class CustomFieldsController extends BaseController
{
	//POSTメソッドでリクエストの場合
	public function post()
	{
		$params = $this -> request -> getRestParams();

		if ($params['data']['Method'] == 'PUT')
		{
			$this -> put();
			return;
		}
		else
		if ($params['data']['Method'] == 'DELETE')
		{
			$this -> delete();
			return;
		}

		$this -> values = $params['data']['custom_fields'];

		if (isset($this -> values['id']))
		{
			unset($this -> values['id']);
		}

		parent::post();
	}

	//PUTメソッドでリクエストの場合
	public function put()
	{
		$params = $this -> request -> getRestParams();
		var_dump($params);

		$this -> values = $params['data']['custom_fields'];

		$this -> where = array('id' => $this -> values['id']);

		parent::put();
	}

	//DELETEメソッドでリクエストの場合
	public function delete()
	{
		$params = $this -> request -> getRestParams();
		var_dump($params);

		$this -> values = $params['data']['CustomField'];

		//$this -> values = $params;

		$this -> where = array('name' => $this -> values['name']);
		print_r($this -> values);
		print_r($this -> where);

		parent::delete();
	}

}
