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
			$sel_cfi -> fields(array(
				'CFI.custom_field_id',
				'CF.name'
			));
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
		$cris = array_column($cris, 'custom_field_id');

		$this -> begin();
		try
		{

			// 読み込み用にtest.csvを開きます。
			$f = fopen("./test.csv", "r");
			// test.csvの行を1行ずつ読み込みます。
			while ($line = fgetcsv($f))
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

	public function search($params)
	{
		$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		$cv = $this -> model('CustomValues', 'CV');

		{
			$sel_cfi = $cfi -> select();
			$sel_cfi -> fields(array(
				'CFI.custom_field_id',
				'CF.name'
			));
			$sel_cfi -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			$sel_cfi -> where('CFI.item_id', $params['id']);
			$rows_cfi = $sel_cfi -> fetchAll();
		}

		{
			$mdl_i_sub = $this -> model('Items', 'I_SUB');

			$sel_iv_sub = $iv -> select();
			$sel_iv_sub -> setJoinAlias('IV_SUB');
			$sel_iv_sub -> fields(array('IV.id'));
			$sel_iv_sub -> joinLeft($mdl_i_sub, array('I_SUB.id = IV.item_id'));
			$sel_iv_sub -> joinLeft($cv, array('CV.item_value_id = IV.id'));
			$sel_iv_sub -> where('I_SUB.id', $params['id']);
			if (isset($params['key']) && $params['key'])
			{
				//全角から半角へ
				$keys = str_replace(array(
					' ',
					'　'
				), ' ', mb_convert_kana($params['key'], 'a', 'UTF-8'));
				//分割
				$keywords = preg_split("/[\s,]+/", $keys);

				$cnt = count($rows_cfi);
				for ($i = 0; $i < $cnt; $i++)
				{
					$custom_field_id = $rows_cfi[$i]['custom_field_id'];
					if ($i == 0)
					{
						// $sel_iv_sub -> where('CV.custom_field_id = ' . $custom_field_id . ' AND CV.value LIKE :key');
						$cnt1 = count($keywords);
						$like_where = ' ( ';
						for ($i1 = 0; $i1 < $cnt1; $i1++)
						{
							if ($i1 == 0)
							{
								$like_where .= ' CV.value LIKE :key' . $i1 . ' ';
							}
							else
							{
								$like_where .= 'AND CV.value LIKE :key' . $i1 . ' ';
							}
						}
						$like_where .= ' ) ';
						$sel_iv_sub -> where('CV.custom_field_id = ' . $custom_field_id . ' AND ' . $like_where);
					}
					else
					{
						//$sel_iv_sub -> orWhere('CV.custom_field_id = ' . $custom_field_id . ' AND CV.value LIKE :key');
						$cnt1 = count($keywords);
						$like_where = ' ( ';
						for ($i1 = 0; $i1 < $cnt1; $i1++)
						{
							if ($i1 == 0)
							{
								$like_where .= ' CV.value LIKE :key' . $i1 . ' ';
							}
							else
							{
								$like_where .= 'AND CV.value LIKE :key' . $i1 . ' ';
							}
						}
						$like_where .= ' ) ';
						$sel_iv_sub -> orWhere('CV.custom_field_id = ' . $custom_field_id . ' AND ' . $like_where);
					}
				}

				// $sel_iv_sub -> params(array('key' => '%' . $params['key'] . '%'));
				$arr_key = array();
				$cnt = count($keywords);
				for ($i = 0; $i < $cnt; $i++)
				{
					$arr_key['key' . $i] = '%' . $keywords[$i] . '%';
				}
				$sel_iv_sub -> params($arr_key);

			}
			$sel_iv_sub -> group('IV.id');
			$sel_iv_sub -> order('IV.id');

			// print_r($sel_iv_sub -> getSql());
			// return;
		}

		{
			$cv_sub = $this -> model('CustomValues', 'CV_SUB');
			$sel_cv_sub = $cv_sub -> select();

			$arr_fields = array(
				'item_value_id',
				'custom_field_id',
				'value'
			);
			$sel_cv_sub -> fields($arr_fields);
			$sel_cv_sub -> joinInner($sel_iv_sub, 'IV_SUB.id = CV_SUB.item_value_id');
			$sel_cv_sub -> group(array('CV_SUB.id'));

			// print_r($sel_cv_sub -> getSql());
			// return;
		}

		{
			$sel_cv = $cv -> select();

			$arr_fields = array('CV_SUB.item_value_id');

			$custom_field_id = null;
			$cnt = count($rows_cfi);
			for ($i = 0; $i < $cnt; $i++)
			{
				$custom_field_id = $rows_cfi[$i]['custom_field_id'];
				$custom_field_name = $rows_cfi[$i]['name'];
				array_push($arr_fields, "MAX( CASE WHEN CV_SUB.custom_field_id = '" . $custom_field_id . "' THEN CV_SUB.value END ) AS " . "'" . $custom_field_name . "'");
			}

			$sel_cv -> fields($arr_fields);
			$sel_cv -> joinInner($sel_cv_sub, array('1 = 1'));
			$sel_cv -> order(array('CV_SUB.item_value_id'));
			$sel_cv -> group(array('CV_SUB.item_value_id'));

			// print_r($sel_cv -> getSql());
			// return;
		}

		$rows = $sel_cv -> fetchAll();
		// var_dump($rows);

		return $rows;
	}

	public function search2($params)
	{
		$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		$cv = $this -> model('CustomValues', 'CV');

		{
			$sel_cfi = $cfi -> select();
			$sel_cfi -> fields(array(
				'CFI.custom_field_id',
				'CF.name'
			));
			$sel_cfi -> joinLeft($cf, array('CF.id = CFI.custom_field_id'));
			$sel_cfi -> where('CFI.item_id', $params['id']);
			$rows_cfi = $sel_cfi -> fetchAll();
		}

		{
			$mdl_i_sub = $this -> model('Items', 'I_SUB');

			$sel_iv_sub = $iv -> select();
			$sel_iv_sub -> setJoinAlias('IV_SUB');
			$sel_iv_sub -> fields(array('IV.id'));
			$sel_iv_sub -> joinLeft($mdl_i_sub, array('I_SUB.id = IV.item_id'));
			$sel_iv_sub -> joinLeft($cv, array('CV.item_value_id = IV.id'));
			$sel_iv_sub -> where('I_SUB.id', $params['id']);
			if (isset($params['key']) && $params['key'])
			{
				$cnt = count($rows_cfi);
				for ($i = 0; $i < $cnt; $i++)
				{
					$custom_field_id = $rows_cfi[$i]['custom_field_id'];
					if ($i == 0)
					{
						$sel_iv_sub -> where('CV.custom_field_id = ' . $custom_field_id . ' AND CV.value LIKE :key');
					}
					else
					{
						$sel_iv_sub -> orWhere('CV.custom_field_id = ' . $custom_field_id . ' AND CV.value LIKE :key');
					}
				}
				$sel_iv_sub -> params(array('key' => '%' . $params['key'] . '%'));
			}
			$sel_iv_sub -> group('IV.id');
			$sel_iv_sub -> order('IV.id');

			// print_r($sel_iv_sub -> getSql());
			// return;
		}

		{
			$cv_sub = $this -> model('CustomValues', 'CV_SUB');
			$sel_cv_sub = $cv_sub -> select();

			$arr_fields = array(
				'item_value_id',
				'custom_field_id',
				'value'
			);
			$sel_cv_sub -> fields($arr_fields);
			$sel_cv_sub -> joinInner($sel_iv_sub, 'IV_SUB.id = CV_SUB.item_value_id');
			$sel_cv_sub -> group(array('CV_SUB.id'));

			// print_r($sel_cv_sub -> getSql());
			// return;
		}

		{
			$sel_cv = $cv -> select();

			$arr_fields = array('CV_SUB.item_value_id');

			$custom_field_id = null;
			$cnt = count($rows_cfi);
			for ($i = 0; $i < $cnt; $i++)
			{
				$custom_field_id = $rows_cfi[$i]['custom_field_id'];
				$custom_field_name = $rows_cfi[$i]['name'];
				array_push($arr_fields, "MAX( CASE WHEN CV_SUB.custom_field_id = '" . $custom_field_id . "' THEN CV_SUB.value END ) AS " . "'" . $custom_field_name . "'");
			}

			$sel_cv -> fields($arr_fields);
			$sel_cv -> joinInner($sel_cv_sub, array('1 = 1'));
			$sel_cv -> order(array('CV_SUB.item_value_id'));
			$sel_cv -> group(array('CV_SUB.item_value_id'));

			// print_r($sel_cv -> getSql());
			// return;
		}

		$rows = $sel_cv -> fetchAll();
		//var_dump($rows);

		return $rows;
	}

	public function search1($params)
	{
		$iv = $this -> model('ItemsValues', 'IV');
		$cf = $this -> model('CustomFields', 'CF');
		$cfi = $this -> model('CustomFieldsItems', 'CFI');
		$cv = $this -> model('CustomValues', 'CV');

		{
			$sel_cfi = $cfi -> select();
			$sel_cfi -> fields(array(
				'CFI.custom_field_id',
				'CF.name'
			));
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
			if (isset($params['key']) && $params['key'])
			{
				//$sel_i_sub -> where('CV.custom_field_id', 9);
				$sel_i_sub -> where('CV.custom_field_id = 9 AND value LIKE :key');
				$sel_i_sub -> orWhere('CV.custom_field_id = 10 AND value LIKE :key');
				$sel_i_sub -> params(array('key' => '%' . $params['key'] . '%'));
				/*
				 $sel_i_sub -> where('CV.custom_field_id', 9);
				 $sel_i_sub->whereLike('CV.value', '%'. $params['key'] .'%');
				 */
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
				array_push($arr_fields, "max( CASE WHEN I_SUB.custom_field_id = '" . $custom_field_id . "' THEN value END ) AS " . "'" . $custom_field_name . "'");
			}

			$sel_i_main -> fields($arr_fields);
			$sel_i_main -> joinInner($sel_i_sub, array('I_SUB.item_id = I_MAIN.id'));
			$sel_i_main -> group(array('I_SUB.value_id'));

			print_r($sel_i_main -> getSql());
			return;
		}

		$rows = $sel_i_main -> fetchAll();
		//var_dump($rows);

		return $rows;
	}

}
