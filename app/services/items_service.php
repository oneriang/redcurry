<?php

class ItemsService extends Service
{
	public function getData($params)
	{
		$res = array();

		$m_i = $this -> model('Items', 'I');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');

		$sel_i = $m_i -> select();
		$sel_i -> fields(array(
			'I.id',
			'I.type',
			'I.name',
			'I.description'
		));
		$sel_i -> where('I.id', $params['id']);

		$res['item'] = $sel_i -> fetchRow(PDO::FETCH_ASSOC);

		$sel = $cf -> select();
		$sel -> fields(array(
			'CF.id',
			'CF.name',
			'CF.description',
			'CFI.item_id'
		));
		$sel -> joinLeft($cfi, array(
			'CFI.custom_field_id = CF.id',
			'CFI.item_id = ' . $params['id']
		));

		$res['custom_fields'] = $sel -> fetchAll();

		return $res;
	}

	//POSTメソッドでリクエストの場合
	public function post($params)
	{
		$items = $this -> model('Items');
		$cfi = $this -> model('CustomFieldsItems');

		$this -> begin();
		try
		{

			$ins = $items -> insert();

			$ins -> values($params['item']);

			$res = $ins -> execute();
			var_dump($res);

			$cnt = count($params['data']['item']['custom_field_ids']);

			for ($i = 0; $i < $cnt; $i++)
			{
				echo $i;

				$ins = $cfi -> insert();

				print_r($params['data']['item']);

				$ins -> values(array(
					'custom_field_id' => $params['data']['item']['custom_field_ids'][$i],
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

	//POSTメソッドでリクエストの場合
	public function put($params)
	{
		$m_i = $this -> model('Items');
		$cfi = $this -> model('CustomFieldsItems');

		$this -> begin();
		try
		{
			$item = $params['data']['item'];

			$upd_i = $m_i -> update();
			$values = array(
				'name' => $item['name'],
				'type' => $item['type'],
				'description' => $item['description']
			);
			$upd_i -> values($values);
			$upd_i -> where('id', $item['id']);
			$res = $upd_i -> execute();

			$del = $cfi -> delete();
			$del -> where('item_id', $params['data']['item']['id']);
			$res = $del -> execute();

			$cnt = count($params['data']['item']['custom_field_ids']);

			for ($i = 0; $i < $cnt; $i++)
			{

				$ins = $cfi -> insert();

				$ins -> values(array(
					'custom_field_id' => $params['data']['item']['custom_field_ids'][$i],
					'item_id' => $params['data']['item']['id']
				));

				$res2 = $ins -> execute();
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
		$i = $this -> model('item', 'I');
		$cf = $this -> model('custom_fields', 'cf');
		$cfi = $this -> model('custom_fields_items', 'cfi');

		$sel = $item -> select();
		$sel -> joinInner($ItemsPrd, array('CP.Items_id = C.Items_id'));
		$sel -> where('C.user_id', userId);
		$sel -> order('CP.add_date DESC');
		$rows = $sel -> fetchAll();

		return $rows;
	}

}
