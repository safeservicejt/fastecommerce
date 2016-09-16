<?php

class controlReport
{
	public function index()
	{
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			$this->adminstats();
		}
		else
		{
			$this->customerstats();
		}

	}


	public function adminstats()
	{
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

	public function customerstats()
	{
		$userid=Users::getCookieUserId();

		$pageData=array();

		$pageData=customerStatsSummary();

		$pageData['listOrders']=Orders::get(array(
			'limitShow'=>20,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where userid='$userid'"
			));

		System::setTitle('Statistics');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('customerStats',$pageData);

		Views::nPanelFooter();
	}




}