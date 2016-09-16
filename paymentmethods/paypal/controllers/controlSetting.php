<?php

class controlSetting
{
	public static function index()
	{
		$pageData=array('alert'=>'');

		if(Request::has('btnAdd'))
		{
			try {
				saveProcess();
				$pageData['alert']='<div class="alert alert-success">Save changes success.</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}

		Views::nPanelHeader();

		Views::make('home',$pageData);

		Views::nPanelFooter();
	}
}