<?php

class controlAffiliate
{
	public static function index()
	{
		$pageData=array('alert'=>'');

		$curPage=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

		Alert::make('Page not found');
		
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		if($owner!='yes')
		{
			Alert::make('Page not found');
		}	

		$userid=Users::getCookieUserId();

		$addWhere='';

		$addPage='';		

		if(Request::has('btnAction'))
		{	
			try {
				actionProcess();
				$pageData['alert']='<div class="alert alert-success">'.Lang::get('usercp/index.completedYourAction').'</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}

		if(Request::has('btnSearch'))
		{
			$txtKeywords=addslashes(trim(Request::get('txtKeywords','')));

			$addWhere="where title LIKE '%$txtKeywords%'";

			$addPage='/search/'.base64_encode($txtKeywords);
		}
	

		$pageData['theList']=Customers::get(array(
			'limitShow'=>20,
			'limitPage'=>$curPage,
			'cache'=>'no',
			'where'=>$addWhere
			));

		$countPost=Customers::get(array(
			'cache'=>'no',
			'selectFields'=>"count(id) as totalRow",
			'where'=>$addWhere
			));

		$pageData['pages']=Misc::genSmallPage(array(
			'url'=>'npanel/plugins/controller/fastecommerce/affiliate/index/'.$addPage,
			'curPage'=>$curPage,
			'limitShow'=>20,
			'limitPage'=>5,
			'showItem'=>count($pageData['theList']),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/20);		

		System::setTitle('Affiliates');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateList',$pageData);

		Views::nPanelFooter();
	}


	public static function report()
	{
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			self::systemReport();
		}
		else
		{
			self::userReport();
		}
	}



	public static function systemReport()
	{

		$userid=Users::getCookieUserId();

		$curPage=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

		$pageData=array();

		$pageData=reportSystemSummary();

		$loadData=AffiliatesStats::get(array(
			'limitShow'=>30,
			'limitPage'=>$curPage,
			'cache'=>'no',
			'cacheTime'=>60,
			));

		$countPost=AffiliatesStats::get(array(
			'cache'=>'no',
			'selectFields'=>"count(id) as totalRow",
			));

		$pageData['pages']=Misc::genSmallPage(array(
			'url'=>'npanel/plugins/controller/fastecommerce/affiliate/report',
			'curPage'=>$curPage,
			'limitShow'=>30,
			'limitPage'=>5,
			'showItem'=>count($loadData),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/30);	

		$total=count($loadData);

		for ($i=0; $i < $total; $i++) { 
			$orderData=Orders::loadCache($loadData[$i]['orderid']);

			if(!$orderData)
			{
				continue;
			}

			$loadData[$i]['orderData']=$orderData;
		}

		$pageData['theList']=$loadData;

		System::setTitle('Statistics');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateSystemReport',$pageData);

		Views::nPanelFooter();
	}

	public static function userReport()
	{

		$userid=Users::getCookieUserId();

		$pageData=array();

		$pageData=reportSummary();

		$loadData=AffiliatesStats::get(array(
			'limitShow'=>30,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where userid='$userid'"
			));

		$total=count($loadData);

		for ($i=0; $i < $total; $i++) { 
			$orderData=Orders::loadCache($loadData[$i]['orderid']);

			if(!$orderData)
			{
				continue;
			}

			$loadData[$i]['orderData']=$orderData;
		}

		$pageData['theList']=$loadData;

		$userData=Customers::loadCache($userid);

		$pageData['rankData']=AffiliatesRanks::loadCache($userData['affiliaterankid']);

		System::setTitle('Statistics');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateReport',$pageData);

		Views::nPanelFooter();
	}

	public static function collection()
	{
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		self::userCollection();
	}

	public static function userCollection()
	{

		$userid=Users::getCookieUserId();


		$pageData=array('alert'=>"");

		if(Request::has('btnAdd'))
		{
			try {
				$result=addCollectionProcess();

				$pageData['alert']='<div class="alert alert-success">'.$result.'</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}


		System::setTitle('Add new collection');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateAddCollection',$pageData);

		Views::nPanelFooter();
	}


	public static function linkbuilding()
	{
		$userid=Users::getCookieUserId();

		$pageData=array();

		$pageData=reportSummary();

		$pageData['listOrders']=Orders::get(array(
			'limitShow'=>30,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where affiliateid='$userid'"
			));

		System::setTitle('Statistics');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('linkBuildingView',$pageData);

		Views::nPanelFooter();
	}

	public static function withdraw()
	{
		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			self::systemWithdraw();
		}
		else
		{
			self::userWithdraw();
		}

	}

	public static function ranks()
	{

		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner!='yes')
		{
			Alert::make('You not have permission to access this page');
		}

		$curPage=0;

		$rankid=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

		if($match=Uri::match('\/edit\/(\d+)'))
		{
			$rankid=$match[1];
		}

		$pageData=array('alert'=>'');

		if(Request::has('btnAction'))
		{
			try {
				actionRankProcess();

				$pageData['alert']='<div class="alert alert-success">Complete your action.</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-success">'.$e->getMessage().'</div>';
			}
		}

		if(Request::has('btnAdd'))
		{
			try {
				addRankProcess();

				$pageData['alert']='<div class="alert alert-success">Add new rank success.</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-success">'.$e->getMessage().'</div>';
			}
		}

		if(Request::has('btnSave'))
		{
			try {
				updateRankProcess($rankid);

				$pageData['alert']='<div class="alert alert-success">Save changes success.</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-success">'.$e->getMessage().'</div>';
			}
		}


		$pageData['ranksList']=AffiliatesRanks::get(array(
			'cache'=>'no',
			'cacheTime'=>60,
			));

		if($match=Uri::match('\/edit\/(\d+)'))
		{
			$rankid=$match[1];

			$loadData=AffiliatesRanks::get(array(
				'cache'=>'no',
				'where'=>"where id='$rankid'"
				));

			if(!isset($loadData[0]['id']))
			{
				Alert::make('This rank not exists.');
			}

			$pageData['rankData']=$loadData[0];

		}


		$loadData=AffiliatesRanks::get(array(
			'limitShow'=>30,
			'limitPage'=>$curPage,
			'cache'=>'no',
			'cacheTime'=>60,
			));

		$countPost=AffiliatesRanks::get(array(
			'cache'=>'no',
			'selectFields'=>"count(id) as totalRow",
			));

		$pageData['pages']=Misc::genSmallPage(array(
			'url'=>'npanel/plugins/controller/fastecommerce/affiliate/ranks',
			'curPage'=>$curPage,
			'limitShow'=>30,
			'limitPage'=>5,
			'showItem'=>count($loadData),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['theList']=$loadData;

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/30);	

		System::setTitle('Affiliates Ranks');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateRanks',$pageData);

		Views::nPanelFooter();
	}

	public static function systemWithdraw()
	{
		$userid=Users::getCookieUserId();

		$curPage=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

		if($match=Uri::match('\/set\/(\w+)\/(\d+)'))
		{
			$method=$match[1];

			$id=$match[2];

			$loadData=AffiliatesWithdraws::get(array(
				'cache'=>'no',
				'where'=>"where id='$id'"
				));

			if(!$loadData)
			{
				Redirects::to(System::getUrl().'npanel/plugins/controller/fastecommerce/affiliate/withdraw');
			}

			$userid=$loadData[0]['userid'];

			$userData=Customers::get(array(
				'cache'=>'no',
				'where'=>"where userid='$userid'"
				));

			switch ($method) {
				case 'completed':

					if($loadData[0]['status']=='completed')
					{
						Redirects::to(System::getUrl().'npanel/plugins/controller/fastecommerce/affiliate/withdraw');
					}

					$balance=(double)$userData[0]['balance']+(double)$loadData[0]['money'];

					AffiliatesWithdraws::update($id,array(
						'status'=>'completed'
						));

					break;
					
				case 'cancel':

					if($loadData[0]['status']=='canceled')
					{
						Redirects::to(System::getUrl().'npanel/plugins/controller/fastecommerce/affiliate/withdraw');
					}

					$balance=(double)$userData[0]['balance']+(double)$loadData[0]['money'];

					AffiliatesWithdraws::update($id,array(
						'status'=>'canceled'
						));

					Customers::update($userid,array(
						'balance'=>$balance
						));

					Customers::saveCache($userid);

					break;
			}

			Redirects::to(System::getUrl().'npanel/plugins/controller/fastecommerce/affiliate/withdraw');
		}

		$pageData=array('requestAlert'=>'');

		$pageData['theList']=AffiliatesWithdraws::get(array(
			'limitShow'=>30,
			'limitPage'=>$curPage,
			'cache'=>'no',
			'cacheTime'=>60,
			));

		$countPost=AffiliatesWithdraws::get(array(
			'cache'=>'no',
			'selectFields'=>"count(id) as totalRow",
			));

		$pageData['pages']=Misc::genSmallPage(array(
			'url'=>'npanel/plugins/controller/fastecommerce/affiliate/withdraw',
			'curPage'=>$curPage,
			'limitShow'=>30,
			'limitPage'=>5,
			'showItem'=>count($pageData['theList']),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/30);	

		System::setTitle('Withdraw');

		Views::nPanelHeader();

		Views::make('addHeader');

		Views::make('affiliateSystemWithdraw',$pageData);

		Views::nPanelFooter();
	}

	public static function userWithdraw()
	{
		$userid=Users::getCookieUserId();

		$pageData=array('requestAlert'=>'');

		if(Request::has('btnSend'))
		{
			try {
				withdrawProcess();

				$pageData['requestAlert']='<div class="alert alert-success">'.Lang::get('usercp/index.createRequestSuccess').'</div>';
			} catch (Exception $e) {
				$pageData['requestAlert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}

		$userData=Customers::get(array(
			'cache'=>'no',
			'where'=>"where userid='$userid'"
			));

		$pageData['userData']=$userData[0];

		$pageData['theList']=AffiliatesWithdraws::get(array(
			'limitShow'=>30,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where userid='$userid'"
			));

		System::setTitle('Withdraw');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateWithdraw',$pageData);

		Views::nPanelFooter();
	}

	public static function billing()
	{
		$userid=Users::getCookieUserId();

		$pageData=array();

		$pageData=reportSummary();

		$pageData['listOrders']=Orders::get(array(
			'limitShow'=>30,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where affiliateid='$userid'"
			));

		System::setTitle('Statistics');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('affiliateReport',$pageData);

		Views::nPanelFooter();
	}



}