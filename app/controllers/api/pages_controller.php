<?php

require_once PathManager::getControllerDirectory() . '/api/base_controller.php';

class PagesController extends BaseController
{
	//POSTメソッドでリクエストの場合
	public function post()
	{
		$params = $this -> request -> getRestParams();
		//
		//var_dump($params);

		//
		// $this -> values = $params['Pages'];
		//
		// parent::post();
		if ($params['data']['Method'] == 'PUT')
		{
			var_dump($params);

			// return;
			$svc = $this -> service('PagesService');
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

		$svc = $this -> service('PagesService');
		$res = $svc -> post($params);
		var_dump($res);
	}

	//PUTメソッドでリクエストの場合
	public function put()
	{
		$params = $this -> request -> getRestParams();
		var_dump($params);

		return;
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

	// //GETメソッドでリクエストの場合
	// public function index()
	// {
	// $item = $this -> model('Pages');
	// print_r($item -> getList());
	//
	// // 結果を文字列としてレスポンス
	// $res['result'] = '0';
	// // JSON形式でレスポンス
	// $this -> response -> json($res);
	// }
	//
	// //POSTメソッドでリクエストの場合
	// public function post()
	// {
	// $params = $this -> request -> getRestParams();
	// print_r($params);
	//
	// $item = $this -> model('Pages');
	//
	// $values = array(
	// 'subject' => $params['subject'],
	// 'description' => $params['description']
	// );
	// $ins = $item -> insert();
	// $ins -> values($values);
	// $res = $ins -> execute();
	// var_dump($res);
	//
	// $this -> redirect('items');
	// }
	//
	// //PUTメソッドでリクエストの場合
	// public function put()
	// {
	// echo "string";
	// $params = $this -> request -> getRestParams();
	// print_r($params);
	//
	// $item = $this -> model('Pages');
	//
	// $values = array(
	// 'subject' => $params['subject'],
	// 'description' => $params['description']
	// );
	// $where = array('subject' => $params['subject']);
	// $upd = $item -> update();
	// $upd -> values($values);
	// $upd -> where($where);
	// $res = $upd -> execute();
	// var_dump($res);
	//
	// $this -> redirect('items');
	// }
	//
	// //DELETEメソッドでリクエストの場合
	// public function delete()
	// {
	// $params = $this -> request -> getRestParams();
	// print_r($params);
	//
	// $item = $this -> model('Pages');
	// $where = array('subject' => $params['subject']);
	// $del = $item -> delete();
	// $del -> where($where);
	// $res = $del -> execute();
	// var_dump($res);
	//
	// $this -> redirect('items');
	// }

}