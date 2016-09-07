<?php

function adminStatsSummary()
{
	$result=array(
		'order'=>0,
		'sale'=>0,
		'customer'=>0,
		'product'=>0,
		);

	$loadData=Orders::get(array(
		'cache'=>'no',
		'selectFields'=>'count(id)as totalRow',
		'where'=>"where prefix='".System::getPrefix()."'"
		));

	$result['order']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Orders::get(array(
		'cache'=>'no',
		'where'=>"where status<>'draft' AND  prefix='".System::getPrefix()."'",
		'selectFields'=>'sum(total)as totalRow'
		));

	$result['sale']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Users::get(array(
		'cache'=>'no',
		'selectFields'=>'count(userid)as totalRow',
		'where'=>"where prefix='".System::getPrefix()."'"
		));

	$result['customer']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Products::get(array(
		'cache'=>'no',
		'selectFields'=>'count(id)as totalRow',
		'where'=>"where prefix='".System::getPrefix()."'"
		));

	$result['product']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	return $result;
}

function customerStatsSummary()
{

	$userid=Users::getCookieUserId();

	$result=array(
		'order'=>0,
		'sale'=>0,
		'customer'=>0,
		'product'=>0,
		);

	$loadData=Orders::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid' AND  prefix='".System::getPrefix()."'",
		'selectFields'=>'count(id)as totalRow'
		));

	$result['order']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Orders::get(array(
		'cache'=>'no',
		'where'=>"where prefix='".System::getPrefix()."' AND userid='$userid' AND status='completed'",
		'selectFields'=>'count(id)as totalRow'
		));

	$result['order_pending']=isset($loadData[0]['totalRow'])?$loadData[0]['totalRow']:0;

	$loadData=Customers::get(array(
		'cache'=>'no',
		'where'=>"where userid='$userid' AND prefix='".System::getPrefix()."'",
		));

	$result['balance']=isset($loadData[0]['balance'])?$loadData[0]['balance']:0;

	$result['commission']=isset($loadData[0]['commission'])?$loadData[0]['commission']:0;

	return $result;
}