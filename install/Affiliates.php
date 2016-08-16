<?php

/*
	

*/

class Affiliates
{
	public static function checkReferShopping()
	{
		$userid=0;

		if(preg_match('/\/(.*?)\?a=(\d+)/i',$_SERVER['REQUEST_URI'],$match))
		{
			$userid=$match[2];
		}

		self::upClick(1,$userid);

		Cookie::make('affiliateid',$userid,1440*30);

		
		if(isset($match[1][5]))
		{
			Redirect::to(System::getUrl().$match[1]);
		}
		
	}

	public static function getAffiliateID()
	{
		$id=isset($_COOKIE['affiliateid'])?$_COOKIE['affiliateid']:0;

		return $id;
	}


	public static function after_insert_order($orderid)
	{
		$orderData=Orders::loadCache($orderid);

		if(!$orderData || $orderData['status']!='completed')
		{
			return false;
		}

		$commission=FastEcommerce::$setting['affiliate_percent'];

		$orderTotal=$orderData['total'];

		$affiliateid=$orderData['affiliateid'];

		$affiliateTotal=((double)$orderTotal*(double)$commission)/100;

		$userData=Customer::loadCache($affiliateid);

		if(!$userData)
		{
			return false;
		}

		$earnTotal=(double)$userData['balance']+(double)$affiliateTotal;

		Customers::update($affiliateid,array(
			'balance'=>$earnTotal
			));

		Customers::saveCache($affiliateid);

		AffiliatesStats::insert(array(
			'userid'=>$userid,
			'money'=>$affiliateTotal,
			'orderid'=>$orderid,
			'status'=>'approved',
			));

	}

	public static function upClick($number=1,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=array();

		if(!file_exists($savePath))
		{
			$result['clicks']=$number;
		}
		else
		{
			$result=unserialize(file_get_contents($savePath));

			$result['clicks']=(int)$result['clicks']+(int)$number;
		}

		File::create($savePath,serialize($result));
	}

	public static function downClick($number=1,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=array();

		if(!file_exists($savePath))
		{
			$result['clicks']=$number;
		}
		else
		{
			$result=unserialize(file_get_contents($savePath));

			$result['clicks']=(int)$result['clicks']-(int)$number;
		}

		File::create($savePath,serialize($result));
	}

	public static function setKey($keyName,$keyVal,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=array();

		if(!file_exists($savePath))
		{
			$result[$keyName]=$keyVal;
		}
		else
		{
			$result=unserialize(file_get_contents($savePath));

			$result[$keyName]=$keyVal;
		}

		File::create($savePath,serialize($result));
	}

	public static function upKey($keyName,$number=1,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=array();

		if(!file_exists($savePath))
		{
			$result[$keyName]=$number;
		}
		else
		{
			$result=unserialize(file_get_contents($savePath));

			$result[$keyName]=(double)$result[$keyName]+(double)$number;
		}

		File::create($savePath,serialize($result));
	}

	public static function downKey($keyName,$number=1,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=array();

		if(!file_exists($savePath))
		{
			$result[$keyName]=$number;
		}
		else
		{
			$result=unserialize(file_get_contents($savePath));

			$result[$keyName]=(double)$result[$keyName]-(double)$number;
		}

		File::create($savePath,serialize($result));
	}

	public static function loadCache($userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=false;

		if(file_exists($savePath))
		{
			$result=unserialize(file_get_contents($savePath));
		}

		return $result;		
	}

	public static function getKey($keyName,$userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=0;

		if(file_exists($savePath))
		{
			$loadData=unserialize(file_get_contents($savePath));

			$result=isset($loadData[$keyName])?$loadData[$keyName]:'';
		}

		return $result;
	}

	public static function getClicks($userid)
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/affiliate/'.$userid.'.cache';

		$result=0;

		if(file_exists($savePath))
		{
			$loadData=unserialize(file_get_contents($savePath));

			$result=$loadData['clicks'];
		}

		return $result;
	}

}