<?php

class IndexController extends Controller
{
	public function index()
	{
		$this -> view -> pageName = $this -> controller;
	}

}
