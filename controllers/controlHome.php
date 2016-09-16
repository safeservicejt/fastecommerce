<?php

class controlHome
{
	public function index()
	{
		Models::load('report');
		
		$pageData=array();

		$pageData=adminStatsSummary();

		$pageData['listOrders']=Orders::get(array(
			'limitShow'=>20,
			'cache'=>'no',
			'cacheTime'=>60,
			));
		
		System::setTitle('Statistics');

		Views::nPanelHeader();

		Views::make('addHeader');

		Views::make('adminStats',$pageData);

		Views::nPanelFooter();
	}

}
