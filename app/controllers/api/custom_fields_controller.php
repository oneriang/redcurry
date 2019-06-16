<?php

require_once PathManager::getControllerDirectory() . '/api/base_controller.php';

class CustomFieldsController extends BaseController
{
	//POSTメソッドでリクエストの場合
	public function post()
	{
		$params = $this -> request -> getRestParams();
		var_dump($params);
		
		if($params['data']['Method'] == 'PUT'){
    $this -> put();
            return;
        }
        else if($params['data']['Method'] == 'DELETE'){
    $this -> delete();
            return;
        }
//var_dump($params['data']['CustomField']);

//unset($params['data']['CustomField']['tracker_id']);

		$this -> values = $params['data']['CustomField'];
		
		
		//var_dump($this -> values);

		parent::post();
	}

	//PUTメソッドでリクエストの場合
	public function put()
	{
		$params = $this -> request -> getRestParams();
var_dump($params);

		$this -> values = $params['data']['CustomField'];

		//$this -> values = $params;

		$this -> where = array('name' => $this -> values['name']);
print_r($this -> values);
print_r($this -> where);
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
