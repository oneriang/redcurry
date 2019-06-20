<?php

class ItemsController extends RestController
{
	private $params;

	//PagesへGETメソッドでリクエストの場合
	public function index()
	{
		$params = $this -> request -> getRestParams();
		$this -> params = $params;

		if (isset($params['action']))
		{
			$action = $params['action'];

			switch ($action)
			{
				case 'list' :
					$this -> _list();
					return;
				case 'add' :
					$this -> _add();
					return;
				case 'edit' :
					if (!isset($params['id']))
					{
						$this -> _list();
						return;
					}

					$this -> _edit();
					return;
			}
		}

		$this -> _list();
		return;
	}

	//
	public function _add()
	{
		$params = $this -> params;

		$m_cf = $this -> model('CustomFields');
		$res = array();
		$res['custom_fields'] = $m_cf -> getList();

		$this -> view -> data = $res;
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	//
	public function _edit()
	{
		$params = $this -> params;

		$svc = $this -> service('ItemsService');
		$res = $svc -> getData($params);

		$this -> view -> data = $res;
		$this -> view -> method = 'PUT';
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	// /itemsへGETメソッドでリクエストの場合
	public function index1()
	{
		$params = $this -> request -> getRestParams();

		$res = array();
		if (isset($params['id']))
		{
			$this -> view -> method = 'PUT';

			$svc = $this -> service('ItemsService');
			$res = $svc -> getData($params);
		}
		else
		{
			$m_cf = $this -> model('CustomFields');
			$res = $m_cf -> getList();
		}

		$this -> view -> data = $res;
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

	//
	public function _list()
	{
		$params = $this -> params;

		$m_i = $this -> model('Items');
		$res = $m_i -> getList();

		$this -> view -> setTemplate('list');
		$this -> view -> items = $res;
		$this -> view -> pageName = $this -> controller;
	}

}
