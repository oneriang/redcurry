<?php

class BaseController extends RestController
{
	/*
	 // リクエストオブジェクト
	 $req = $this -> request;
	 // コントローラー名
	 $controller = $req -> getController();
	 // アクション名
	 $action = $req -> getAction();
	 // コントローラーサブディレクトリ
	 $subdir = $req -> getControllerSubDirectory();
	 // POST
	 $post = $req -> getPost();
	 // GET
	 $query = $req -> getQuery();
	 // URLパラメーター
	 $params = $req -> getparams();
	 */

	/**
	 * クラス名.
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * INSERT文で値を設定するするフィールドと値.
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * where条件.
	 *
	 * @var array
	 */
	protected $where;

	public function preProcess()
	{
		// リクエストオブジェクト
		$req = $this -> request;
		// コントローラー名
		$controller = $req -> getController();
    
		$this -> className = ucfirst(strtr(ucwords(strtr($controller, ['_' => ' '])), [' ' => '']));
	}

	//GETメソッドでリクエストの場合
	public function index()
	{
		$mdl = $this -> model($this -> className);
		$data = $mdl -> getList();

		// 結果を文字列としてレスポンス
		$res['result'] = TRUE;
		$res['data'] = $data;
		// JSON形式でレスポンス
		$this -> response -> json($res);
	}

	//POSTメソッドでリクエストの場合
	public function post()
	{
		$mdl = $this -> model($this -> className);

		$ins = $mdl -> insert();

		$ins -> values($this -> values);

		$res = $ins -> execute();

		var_dump($res);
	}

	//PUTメソッドでリクエストの場合
	public function put()
	{
		$mdl = $this -> model($this -> className);

		$upd = $mdl -> update();

		$upd -> values($this -> values);

		$upd -> where($this -> where);

		$res = $upd -> execute();

		var_dump($res);
	}

	//DELETEメソッドでリクエストの場合
	public function delete()
	{
		$mdl = $this -> model($this -> className);

		$del = $mdl -> delete();

		$del -> where($this -> where);

		$res = $del -> execute();

		var_dump($res);
	}

}
