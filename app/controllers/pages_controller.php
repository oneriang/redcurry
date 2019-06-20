<?php

class PagesController extends RestController
{
	private $params;

	//PagesへGETメソッドでリクエストの場合
	public function index()
	{
		$params = $this -> request -> getRestParams();
		$this -> params = $params;

		if (isset($params['id']))
		{
			$id = $params['id'];

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
						if (!isset($params['value_id']))
						{
							$this -> _list();
							return;
						}
						$this -> _edit();
						return;
					case 'import' :
						$this -> _import();
						return;
				}
			}
			else
			{
				$this -> _add();
				return;
			}
		}

		$this -> _pageList();
	}

	//
	public function _add()
	{
		$params = $this -> params;

		$svc = $this -> service('PagesService');

		$res = $svc -> getFields($params);

		$this -> view -> setTemplate($this -> controller);
		$this -> view -> method = 'POST';
		$this -> view -> items = $res;
		$this -> view -> pageId = $params['id'];
		$this -> view -> valueId = $params['value_id'];
		$this -> view -> pageName = $this -> controller;
	}

	//
	public function _edit()
	{
		$params = $this -> params;

		$svc = $this -> service('PagesService');

		$res = $svc -> getData($params);

		$this -> view -> setTemplate($this -> controller);
		$this -> view -> method = 'PUT';
		$this -> view -> items = $res;
		$this -> view -> pageId = $params['id'];
		$this -> view -> valueId = $params['value_id'];
		$this -> view -> pageName = $this -> controller;
	}

	//
	public function _list()
	{
		$params = $this -> params;

		$svc = $this -> service('PagesService');

		$res = $svc -> getDataList($params);
		$this -> view -> setTemplate('list');

		$this -> view -> items = $res;
		$this -> view -> pageId = $params['id'];
		$this -> view -> valueId = $params['value_id'];
		$this -> view -> pageName = $this -> controller;
	}

	//
	public function _pageList()
	{
		$params = $this -> params;

		$m_i = $this -> model('Items');
		$res = $m_i -> getListByType('page');

		$this -> view -> setTemplate('page_list');
		$this -> view -> items = $res;
		$this -> view -> pageName = $this -> controller;
	}
	
//
	public function _import()
	{
		$params = $this -> params;

		$svc = $this -> service('PagesService');

		$res = $svc -> import($params);
		
		$this -> view -> setTemplate('list');

		$this -> view -> items = $res;
		$this -> view -> pageId = $params['id'];
		$this -> view -> valueId = $params['value_id'];
		$this -> view -> pageName = $this -> controller;
	}

}