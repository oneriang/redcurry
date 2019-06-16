<?php

class CustomFields extends Model
{

	public function getList()
	{
		$sel = $this -> select();
		$rows = $sel -> fetchAll();
		return $rows;
	}

	public function getColumns()
	{
		$db = Db::factory();
		$stmt = $db -> query("PRAGMA table_info(custom_fields)");
		$rows = $stmt -> fetchAll();
		return $rows;
	}

}
