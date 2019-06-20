<?php

class PagesService extends Service
{

	public function getFields($params)
	{
		//$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		//$cv = $this -> model('CustomValues', 'CV');

			//$i = $this -> model('Items', 'I');

			$sel = $cf -> select();
			$sel -> fields(array(
				'CF.id',
				'CF.name'
				//,
				//'CV.value'
			));
			$sel -> joinLeft($cfi, array('CFI.custom_field_id = CF.id'));
			//$sel -> joinLeft($iv, array('IV.item_id = I.id'));
			//$sel -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			//$sel -> joinLeft($cv, array(
			//	'CF.id = CV.custom_field_id',
			//	'IV.id = CV.item_value_id'
			//));

			$sel -> where('CFI.item_id', $params['id']);
			//$sel -> where('CV.item_value_id', $params['value_id']);

			$rows = $sel -> fetchAll();

			var_dump($rows);

		return $rows;
	}

	public function getDataList($params)
	{
		$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		$cv = $this -> model('CustomValues', 'CV');

		{
			$sel_cfi = $cfi -> select();
			$sel_cfi -> fields(array('CFI.custom_field_id','CF.name'));
			$sel_cfi -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			$sel_cfi -> where('CFI.item_id', $params['id']);
			$rows_cfi = $sel_cfi -> fetchAll();
			//var_dump($rows_cfi);
		}

		{
			$mdl_i_sub = $this -> model('Items', 'I');
			$sel_i_sub = $mdl_i_sub -> select();
			$sel_i_sub -> setJoinAlias('I_SUB');
			$sel_i_sub -> fields(array(
				'I.id AS item_id',
				'CV.item_value_id AS value_id',
				'CF.id AS custom_field_id',
				'CV.value'
			));
			$sel_i_sub -> joinLeft($cfi, array('CFI.item_id = I.id'));
			$sel_i_sub -> joinLeft($iv, array('IV.item_id = I.id'));
			$sel_i_sub -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			$sel_i_sub -> joinLeft($cv, array(
				'CV.custom_field_id = CF.id',
				'CV.item_value_id = IV.id'
			));
			$sel_i_sub -> where('I.id', $params['id']);
			if (isset($params['value_id']) && $params['value_id'])
			{
				$sel_i_sub -> where('CV.item_value_id', $params['value_id']);
			}
			$sel_i_sub -> order('CV.item_value_id');
		}

		{
			$mdl_i_main = $this -> model('Items', 'I_MAIN');
			$sel_i_main = $mdl_i_main -> select();

			$arr_fields = array();
			array_push($arr_fields, "I_SUB.value_id");

			$custom_field_id = null;
			$cnt = count($rows_cfi);
			for ($i = 0; $i < $cnt; $i++)
			{
				// $custom_field_id = $rows_cfi[$i]['custom_field_id'];
				// array_push($arr_fields, "max( CASE WHEN I_SUB.custom_field_id = '" . $custom_field_id . "' THEN value END ) AS cfi_" . $custom_field_id);
				$custom_field_id = $rows_cfi[$i]['custom_field_id'];
				$custom_field_name = $rows_cfi[$i]['name'];
				array_push($arr_fields, "max( CASE WHEN I_SUB.custom_field_id = '" . $custom_field_id . "' THEN value END ) AS " . $custom_field_name);
			}

			$sel_i_main -> fields($arr_fields);
			$sel_i_main -> joinInner($sel_i_sub, array('I_SUB.item_id = I_MAIN.id'));
			$sel_i_main -> group(array('I_SUB.value_id'));
		}

		$rows = $sel_i_main -> fetchAll();
		//var_dump($rows);

		return $rows;
	}

	public function getData($params)
	{
		$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		$cv = $this -> model('CustomValues', 'CV');

			$i = $this -> model('Items', 'I');

			$sel = $i -> select();
			$sel -> fields(array(
				'CF.id',
				'CF.name',
				'CV.value'
			));
			$sel -> joinLeft($cfi, array('CFI.item_id = I.id'));
			$sel -> joinLeft($iv, array('IV.item_id = I.id'));
			$sel -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			$sel -> joinLeft($cv, array(
				'CF.id = CV.custom_field_id',
				'IV.id = CV.item_value_id'
			));

			$sel -> where('I.id', $params['id']);
			$sel -> where('CV.item_value_id', $params['value_id']);

			$rows = $sel -> fetchAll();

			//var_dump($rows);

		return $rows;
	}

	//POSTメソッドでリクエストの場合
	public function post($params)
	{
		$iv = $this -> model('ItemsValues');
		$cv = $this -> model('CustomValues');

		$this -> begin();
		try
		{
			$ins_iv = $iv -> insert();
			$ins_iv -> values(array('item_id' => $params['data']['page']['id']));
			$iv_id = $ins_iv -> execute();

			$cnt = count($params['data']['page']['items']);

			for ($i = 0; $i < $cnt; $i++)
			{
				echo $i;

				$ins_cv = $cv -> insert();

				$ins_cv -> values(array(
					'item_value_id' => $iv_id,
					'custom_field_id' => $params['data']['page']['items'][$i]['custom_field_id'],
					'value' => $params['data']['page']['items'][$i]['value']
				));

				$res2 = $ins_cv -> execute();
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

	//PUTメソッドでリクエストの場合
	public function put($params)
	{
		//var_dump($params);

		$cv = $this -> model('CustomValues');

		$this -> begin();
		try
		{
			$cnt = count($params['data']['page']['items']);

			for ($i = 0; $i < $cnt; $i++)
			{
				$upd = $cv -> update();

				$upd -> values(array('value' => $params['data']['page']['items'][$i]['value']));

				$upd -> where(array(
					'item_value_id' => $params['data']['page']['value_id'],
					'custom_field_id' => $params['data']['page']['items'][$i]['custom_field_id']
				));

				$res2 = $upd -> execute();
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
	public function import($params)
	{
		
		$params['item_id'] = 46;
		
		$iv = $this -> model('ItemsValues');
		$cv = $this -> model('CustomValues');
		$cfi = $this -> model('CustomFieldsItems');
		
		$sel_cfi = $cfi -> select();
		$sel_cfi -> fields('custom_field_id');
		$sel_cfi -> where('item_id', $params['item_id']);
		//$cris = $sel_cfi -> fetchAll(PDO::FETCH_COLUMN,0);
		$cris = $sel_cfi -> fetchAll();
		//var_dump($cris);
		//var_dump(array_column($cris,'custom_field_id'));
		//return;
$cris = array_column($cris,'custom_field_id');

		$this -> begin();
		try
		{
			
  // 読み込み用にtest.csvを開きます。 
		$f = fopen("./test.csv", "r"); 
		// test.csvの行を1行ずつ読み込みます。 
		while($line = fgetcsv($f))
		{
			$ins_iv = $iv -> insert();
			$ins_iv -> values(array('item_id' => $params['item_id']));
			$iv_id = $ins_iv -> execute();
 
		// 読み込んだ結果を表示します。 
			//var_dump($line);
			
			$cnt = count($line);
			
			for ($i = 0; $i < $cnt; $i++)
			{
				//echo $i;

				$ins_cv = $cv -> insert();
				
			$ins_cv -> values(array(
					'item_value_id' => $iv_id,
					'custom_field_id' => $cris[$i],
					'value' => $line[$i]
			));

		//	print_r($ins_cv->getSql());
				$res2 = $ins_cv -> execute();
				
			//	var_dump($res2);
	
			}
			
			}
			
		
					
			// test.csvを閉じます。	
			fclose($f);

			$this -> commit();

			return true;

		}
		catch (Exception $e)
		{
			$this -> rollback();
			throw $e;
		}
	}

}