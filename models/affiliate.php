<?php

function addCollectionProcess()
{
	$send=Request::get('send');


	$listID=$send['id'];

	if(!preg_match_all('/(\d+)/i', $listID, $matches))
	{
		throw new Exception('You have to type product id for create collection.');
		
	}

	$userid=Users::getCookieUserId();

	$listID="'".implode("','", $matches[1])."'";

	$colHas=CollectionsProducts::saveCache($userid,$listID);

	return 'Create collection success. Click <a href="'.CollectionsProducts::url($colHas).'" target="_blank">here</a> to view your collection!';
}

function withdrawProcess()
{
	$money_request=(double)Request::get('money_request',0);

	if($money_request==0)
	{
		throw new Exception(Lang::get('usercp/index.moneyMustLargeThan').'0.00');
	}

	$userid=Users::getCookieUserId();

	$userData=Customers::loadCache($userid);

	if((double)$userData['balance']<(double)$money_request)
	{
		throw new Exception(Lang::get('usercp/index.youNotEnoughMoneyForCreateRequest'));
		
	}

	$balance=(double)$userData['balance']-(double)$money_request;

	Customers::update($userid,array(
		'balance'=>$balance
		));

	Customers::saveCache($userid);


}

function reportSystemSummary()
{

	$userid=Users::getCookieUserId();

	$result=array(
		'clicks'=>0,
		'sale'=>0,
		'customer'=>0,
		'withdrawed'=>0,
		);

	$result['clicks']=Affiliates::getClicks($userid);

	$result['withdrawed']=(double)Affiliates::getKey('withdrawed',$userid);

	$loadData=Orders::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid' AND status='completed'",
		'selectFields'=>'count(id)as totalRow'
		));

	$result['order_completed']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Customers::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid'",
		));

	$result['balance']=isset($loadData[0]['balance'])?$loadData[0]['balance']:0;

	$result['commission']=isset($loadData[0]['commission'])?$loadData[0]['commission']:0;

	return $result;
}

function reportSummary()
{

	$userid=Users::getCookieUserId();

	$result=array(
		'clicks'=>0,
		'sale'=>0,
		'customer'=>0,
		'withdrawed'=>0,
		);

	$result['clicks']=Affiliates::getClicks($userid);

	$result['withdrawed']=(double)Affiliates::getKey('withdrawed',$userid);

	$loadData=Orders::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid' AND status='completed'",
		'selectFields'=>'count(id)as totalRow'
		));

	$result['order_completed']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Customers::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid'",
		));

	$result['balance']=isset($loadData[0]['balance'])?$loadData[0]['balance']:0;

	$result['commission']=isset($loadData[0]['commission'])?$loadData[0]['commission']:0;

	return $result;
}