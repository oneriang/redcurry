<?php

class ItemsService extends Service
{
    public function getData($params)
	{
		//$sel = $this -> select();
	//	$sel->where('id', $params['id']);
	//	$rows = $sel -> fetchAll();
	//	return $rows;
		
$i = $this -> model('Items', 'I');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');

		
		$sel = $i -> select();
		$sel->fields(array(
           // 'I.subject',
            'CF.id',
            'CF.name'
        ));
		$sel -> joinInner($cfi, array('I.id = CFI.item_id'));
	$sel -> joinInner($cf, array('CF.id = CFI.custom_field_id'));
	
		$sel -> where('I.id', $params['id']);
		//$sel -> where('I.subject', $params['subject']);
		//$sel -> order('CP.add_date DESC');
		$rows = $sel -> fetchAll();
		
//var_dump($rows);

return $rows;
	}
	
	//POSTメソッドでリクエストの場合
	public function post($params)
	{
		var_dump($params);

		// parent::post();

		$items = $this -> model('Items');
		$cfi = $this -> model('CustomFieldsItems');

		$this -> begin();
		try
		{

			$ins = $items -> insert();

			$ins -> values($params['Items']);

			$res = $ins -> execute();
			var_dump($res);
			
			$cnt = count($params['data']['Items']['custom_field_ids']);

for ($i = 0; $i < $cnt; $i++) {
    echo $i;

			$ins = $cfi -> insert();
			
			print_r($params['data']['Items']);

			$ins -> values(array(
				'custom_field_id' => $params['data']['Items']['custom_field_ids'][$i],
				'item_id' => $res
			));

			$res2 = $ins -> execute();
			var_dump($res2);
}
			$this -> commit();

			return true;

		}
		catch (Exception $e)
		{
			$this -> rollback();
			throw $e;
		}
	}

	public function getItemsInfo($userId)
	{
		$i = $this -> model('Items', 'I');
		$cf = $this -> model('custom_fields', 'cf');
		$cfi = $this -> model('custom_fields_items', 'cfi');

		$sel = $Items -> select();
		$sel -> joinInner($ItemsPrd, array('CP.Items_id = C.Items_id'));
		$sel -> where('C.user_id', userId);
		$sel -> order('CP.add_date DESC');
		$rows = $sel -> fetchAll();

		return $rows;
	}

}
