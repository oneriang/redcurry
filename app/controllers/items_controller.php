<?php

class ItemsController extends RestController
{
	// /itemsへGETメソッドでリクエストの場合
	public function index()
	{
		$params = $this -> request -> getRestParams();

		$svc = $this -> service('ItemsService');
		$res = $svc -> getData($params);
		var_dump($res);

		$params = $this -> request -> getRestParams();

		$m_cf = $this -> model('CustomFields');

		$this -> view -> customFields = $m_cf -> getList();
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	// /itemsへGETメソッドでリクエストの場合
	public function edit()
	{
		$item = $this -> model('Items');
		// print_r($item -> getList());

		$mdl_cf = $this -> model('CustomFields');
		//print_r($mdl -> getColumns());
		// print_r($mdl_cf -> getList());

		$mdl_cfi = $this -> model('CustomFieldsItems');
		print_r($mdl_cfi -> getList());

		$this -> view -> customFields = $mdl_cf -> getList();
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

}
