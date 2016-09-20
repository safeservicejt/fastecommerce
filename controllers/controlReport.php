<?php

class controlReport
{
	public static function index()
	{
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			self::adminstats();
		}
		else
		{
			self::customerstats();
		}

	}


	public static function adminstats()
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

	public static function customerstats()
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