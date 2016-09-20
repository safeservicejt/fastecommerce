<?php

class controlPaymentmethod
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

		$pageData['theList']=theList();
	
		System::setTitle('Payment methods');

		Views::nPanelHeader();

		

		Views::make('addHeader');

		Views::make('paymentMethodList',$pageData);

		Views::nPanelFooter();
	}

	public function activate()
	{
		$folderName=0;

		if($match=Uri::match('\/activate\/(\w+)'))
		{
			$folderName=trim(strtolower($match[1]));
		}

		if(!isset($folderName[1]))
		{
			Alert::make('Data not valid.');
		}

		$filePath=ROOT_PATH.'contents/plugins/fastecommerce/paymentmethods/'.$folderName.'/info.txt';

		if(!file_exists($filePath))
		{
			Alert::make('This payment method not exists.');
		}

		$theData=file($filePath);

		$loadData=Payments::get(array(
			'cache'=>'no',
			'where'=>"where foldername='$folderName'"
			));

		if(isset($loadData[0]['foldername']))
		{
			Payments::update(0,array(
				'status'=>1
				),"foldername='$folderName'");	
		}
		else
		{
			Payments::insert(array(
				'foldername'=>$folderName,
				'status'=>1,
				'title'=>ucfirst($theData[0])
				));
		}
	
		Payments::saveCache($folderName);

		Payments::saveToCache();

		$theList=FastEcommerce::$setting['payments'];

		if(!in_array($folderName, $theList))
		{
			FastEcommerce::$setting['payments'][]=$folderName;

			FastEcommerce::saveSetting(array('payments'=>FastEcommerce::$setting['payments']));
		}		

		Redirects::to(Views::url('paymentmethod','index'));
	}

	public function deactivate()
	{
		$folderName=0;

		if($match=Uri::match('\/deactivate\/(\w+)'))
		{
			$folderName=trim(strtolower($match[1]));
		}

		if(!isset($folderName[1]))
		{
			Alert::make('Data not valid.');
		}

		$filePath=ROOT_PATH.'contents/plugins/fastecommerce/paymentmethods/'.$folderName.'/info.txt';

		if(!file_exists($filePath))
		{
			Alert::make('This payment method not exists.');
		}

		$theData=file($filePath);

		$loadData=Payments::get(array(
			'cache'=>'no',
			'where'=>"where foldername='$folderName'"
			));


		if(isset($loadData[0]['foldername']))
		{
			Payments::update(0,array(
				'status'=>0
				),"foldername='$folderName'");	
		}
		else
		{
			Payments::insert(array(
				'foldername'=>$folderName,
				'status'=>0,
				'title'=>ucfirst($theData[0])
				));
		}

		Payments::removeCache(array(0=>$folderName));

		Payments::saveToCache();

		$theList=FastEcommerce::$setting['payments'];

		if(in_array($folderName, $theList))
		{
			$pos=array_search($folderName, $theList);

			unset(FastEcommerce::$setting['payments'][$pos]);

			FastEcommerce::saveSetting(array('payments'=>FastEcommerce::$setting['payments']));
		}

		Redirects::to(Views::url('paymentmethod','index'));
	}

	public function setting()
	{
		$pageData=array('alert'=>'');

		$folderName=0;

		$route='index';

		if($match=Uri::match('\/setting\/(\w+)'))
		{
			$folderName=$match[1];
		}

		if($match=Uri::match('\/setting\/(\w+)\/(\w+)'))
		{
			$route=$match[2];
		}

		$savePath=ROOT_PATH.'contents/plugins/fastecommerce/paymentmethods/'.$folderName.'/';

		$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

		if($owner!='yes')
		{
			Alert::make('Page not found');
		}


		$loadData=file($savePath.'info.txt');

		$pageData['title']=$loadData[0].' - '.$loadData[1];

		$controlName='setting';

		$funcName='index';

		if($match=Uri::match('fastecommerce\/paymentmethod\/setting\/paypal\/(\w+)'))
		{
			$controlName=$match[1];

			if($match=Uri::match('fastecommerce\/paymentmethod\/setting\/paypal\/(\w+)\/(\w+)'))
			{
				$funcName=$match[2];
			}

		}

		System::setTitle($pageData['title']);

		Controllers::load($controlName,$funcName,'contents/plugins/fastecommerce/paymentmethods/'.$folderName);

		// include($savePath.'controllers/controlSetting.php');

		// if(file_exists($savePath.'models/setting.php'))
		// {
		// 	include($savePath.'models/setting.php');
		// }

		// if(!class_exists('controlSetting') || !method_exists('controlSetting', $route))
		// {
		// 	Alert::make('This payment method not support setting');
		// }

		// define('THIS_VIEW_PATH',$savePath.'views/');
	
		// System::setTitle($pageData['title']);

		// Views::nPanelHeader();

		// Views::make('addHeader');

		// controlSetting::$route();

		// Views::nPanelFooter();
	}



}