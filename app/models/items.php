<?php

class Items extends Model
{
	public function getData($params)
	{
		$sel = $this -> select();
		$sel -> where('id', $params['id']);
		$rows = $sel -> fetchAll();
		return $rows;
	}

	public function getList()
	{
		$sel = $this -> select();
		$rows = $sel -> fetchAll();
		return $rows;
	}

	public function getListByType($type)
	{
		try
		{
			$sel = $this -> select();
			$sel -> where('type', $type);
			$rows = $sel -> fetchAll();
			return $rows;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

}
