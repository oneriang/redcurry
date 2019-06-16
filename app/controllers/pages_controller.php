<?php

class PagesController extends RestController
{
	// /PagesへGETメソッドでリクエストの場合
	public function index()
	{
		$params = $this -> request -> getRestParams();

		$svc = $this -> service('PagesService');
		$res = $svc -> getData($params);

		$this -> view -> method = 'POST';

		if (isset($params['value_id']))
		{
			$this -> view -> method = 'PUT';
		}

		$this -> view -> items = $res;
		$this -> view -> pageId = $params['id'];
		$this -> view -> valueId = $params['value_id'];
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

}
