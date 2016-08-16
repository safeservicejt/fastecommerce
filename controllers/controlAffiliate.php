<?php

class controlAffiliate
{
	public function index()
	{
		$pageData=array('alert'=>'');

		$curPage=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

		$owner=UserGroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

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
			'url'=>'admincp/plugins/privatecontroller/fastecommerce/affiliate'.$addPage,
			'curPage'=>$curPage,
			'limitShow'=>20,
			'limitPage'=>5,
			'showItem'=>count($pageData['theList']),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/20);		

		System::setTitle('Affiliates');

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateList',$pageData);

		CtrPlugin::admincpFooter();
	}


	public function report()
	{
		$owner=UserGroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			$this->systemReport();
		}
		else
		{
			$this->userReport();
		}
	}



	public function systemReport()
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
			'url'=>'admincp/plugins/privatecontroller/fastecommerce/affiliate/report',
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

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateSystemReport',$pageData);

		CtrPlugin::admincpFooter();
	}

	public function userReport()
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

		System::setTitle('Statistics');

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateReport',$pageData);

		CtrPlugin::admincpFooter();
	}

	public function collection()
	{
		$owner=UserGroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		$this->userCollection();
	}

	public function userCollection()
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

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateAddCollection',$pageData);

		CtrPlugin::admincpFooter();
	}


	public function linkbuilding()
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

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('linkBuildingView',$pageData);

		CtrPlugin::admincpFooter();
	}

	public function withdraw()
	{
		$owner=UserGroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		$userid=Users::getCookieUserId();

		if($owner=='yes')
		{
			$this->systemWithdraw();
		}
		else
		{
			$this->userWithdraw();
		}

	}

	public function systemWithdraw()
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
				Redirect::to(System::getUrl().'admincp/plugins/privatecontroller/fastecommerce/affiliate/withdraw');
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
						Redirect::to(System::getUrl().'admincp/plugins/privatecontroller/fastecommerce/affiliate/withdraw');
					}

					$balance=(double)$userData[0]['balance']+(double)$loadData[0]['money'];

					AffiliatesWithdraws::update($id,array(
						'status'=>'completed'
						));

					break;
					
				case 'cancel':

					if($loadData[0]['status']=='canceled')
					{
						Redirect::to(System::getUrl().'admincp/plugins/privatecontroller/fastecommerce/affiliate/withdraw');
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

			Redirect::to(System::getUrl().'admincp/plugins/privatecontroller/fastecommerce/affiliate/withdraw');
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
			'url'=>'admincp/plugins/privatecontroller/fastecommerce/affiliate/withdraw',
			'curPage'=>$curPage,
			'limitShow'=>30,
			'limitPage'=>5,
			'showItem'=>count($pageData['theList']),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/30);	

		System::setTitle('Withdraw');

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateSystemWithdraw',$pageData);

		CtrPlugin::admincpFooter();
	}

	public function userWithdraw()
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

		$pageData['userData']=Customers::get(array(
			'cache'=>'no',
			'where'=>"where userid='$userid'"
			));

		$pageData['theList']=AffiliatesWithdraws::get(array(
			'limitShow'=>30,
			'cache'=>'no',
			'cacheTime'=>60,
			'where'=>"where userid='$userid'"
			));

		System::setTitle('Withdraw');

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateWithdraw',$pageData);

		CtrPlugin::admincpFooter();
	}

	public function billing()
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

		CtrPlugin::admincpHeader();

		CtrPlugin::admincpLeft();

		CtrPlugin::view('addHeader');

		CtrPlugin::view('affiliateReport',$pageData);

		CtrPlugin::admincpFooter();
	}



}