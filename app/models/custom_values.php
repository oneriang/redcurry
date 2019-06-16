<?php

class CustomValues extends Model
{
    public function getData($params)
	{
		$sel = $this -> select();
		$sel->where('id', $params['id']);
		$rows = $sel -> fetchAll();
		return $rows;
	}
	
	public function getList()
	{
		$sel = $this -> select();
		$rows = $sel -> fetchAll();
		return $rows;
	}
}
