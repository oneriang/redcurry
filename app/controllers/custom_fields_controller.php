<?php

class CustomFieldsController extends RestController
{
	// /itemsへGETメソッドでリクエストの場合
	public function index()
	{
		$mdl = $this -> model('CustomFields');
		//print_r($mdl -> getColumns());
		//print_r($mdl -> getList());


		$this -> view -> columns = $mdl -> getColumns();
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

}
