<?php

class CustomFieldsController extends RestController
{
	private $params;

	//
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

	// /itemsへGETメソッドでリクエストの場合
	public function index1()
	{
		$mdl = $this -> model('CustomFields');
		$this -> view -> columns = $mdl -> getColumns();
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	//
	public function _add()
	{
		$params = $this -> params;

		$m_cf = $this -> model('CustomFields');
		$res = $m_cf -> getColumns();
		$this -> view -> columns = $res;
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	//
	public function _edit()
	{
		$params = $this -> params;

		$m_cf = $this -> model('CustomFields');
		$columns = $m_cf -> getColumns();
		$custom_field = $m_cf -> getData($params);
		//var_dump($custom_field);

		$this -> view -> method = 'PUT';
		$this -> view -> columns = $columns;
		$this -> view -> custom_field = $custom_field;
		$this -> view -> pageName = $this -> controller;
		$this -> view -> setTemplate($this -> controller);
	}

	//
	public function _list()
	{
		$params = $this -> params;

		$m_cf = $this -> model('CustomFields');
		$res = $m_cf -> getList();

		$this -> view -> setTemplate('list');
		$this -> view -> items = $res;
		$this -> view -> pageName = $this -> controller;
	}

}
