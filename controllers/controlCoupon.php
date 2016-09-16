<?php

class controlCoupon
{
	public function index()
	{
		$pageData=array('alert'=>'');

		$curPage=0;

		if($match=Uri::match('\/page\/(\d+)'))
		{
			$curPage=$match[1];
		}

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
				$pageData['alert']='<div class="alert alert-success">Completed your action.</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}


		$pageData['theList']=Coupons::get(array(
			'limitShow'=>20,
			'limitPage'=>$curPage,
			'cache'=>'no',
			'where'=>$addWhere
			));

		$countPost=Coupons::get(array(
			'cache'=>'no',
			'selectFields'=>"count(id) as totalRow",
			'where'=>$addWhere
			));

		$pageData['pages']=Misc::genSmallPage(array(
			'url'=>'npanel/plugins/controller/fastecommerce/coupon/index/'.$addPage,
			'curPage'=>$curPage,
			'limitShow'=>20,
			'limitPage'=>5,
			'showItem'=>count($pageData['theList']),
			'totalItem'=>$countPost[0]['totalRow'],
			));

		$pageData['totalPost']=$countPost[0]['totalRow'];

		$pageData['totalPage']=intval((int)$countPost[0]['totalRow']/20);		

		System::setTitle('Coupons');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('couponList',$pageData);

		Views::nPanelFooter();
	}

	public function addnew()
	{
		$pageData=array('alert'=>'');

		if(Request::has('btnAdd'))
		{
			try {
				insertProcess();

				
				$pageData['alert']='<div class="alert alert-success">Add coupon completed</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}


		// $pageData['listBrand']=Brands::get(array(
		// 	'orderby'=>'order by title asc',
		// 	'cache'=>'no'
		// 	));	

		System::setTitle('Add Coupon');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('couponAdd',$pageData);

		Views::make('addFooter');

		Views::nPanelFooter();
	}

	public function edit()
	{
		$pageData=array('alert'=>'');

		$id=0;

		if($match=Uri::match('\/edit\/(\d+)'))
		{
			$id=$match[1];
		}
		else
		{
			Alert::make('Page not found');
		}

		if(Request::has('btnSave'))
		{
			try {
				updateProcess($id);
				$pageData['alert']='<div class="alert alert-success">Save changes completed</div>';
			} catch (Exception $e) {
				$pageData['alert']='<div class="alert alert-warning">'.$e->getMessage().'</div>';
			}
		}

		$loadData=Coupons::get(array(
			'cache'=>'no',
			'isHook'=>'no',
			'where'=>"where id='$id'"
			));

		if(!isset($loadData[0]['id']))
		{
			Alert::make('Page not found');
		}

		$pageData['theData']=$loadData[0];


		// $pageData['listBrand']=Brands::get(array(
		// 	'orderby'=>'order by title asc',
		// 	'cache'=>'no'
		// 	));	

		System::setTitle('Edit Coupon');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('couponEdit',$pageData);

		Views::make('addFooter');

		Views::nPanelFooter();
	}



}