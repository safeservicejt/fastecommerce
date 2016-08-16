<?php

class CollectionsProducts
{

	public static function get()
	{
		$uri=System::getUri();


		$result=array();

		if(preg_match('/collection\/render\/\d+/i', $uri))
		{
			if(preg_match_all('/(\d+)/i', $uri, $matches))
			{
				$listID=implode(',', $matches[1]);


				$userid=(int)Users::getCookieUserId();

				$hash=self::saveCache($userid,$listID);

				Redirect::to('collection/'.$hash.'.html');
			}
		}
		elseif(preg_match('/collection\/([a-zA-Z0-9_]+)\.html$/i', $uri,$match))
		{
			$hash=$match[1];

			$result=self::loadCache($hash);

			return $result;
		}
		else
		{
			Redirect::to('404page');
		}
	}

	public static function exists($id='')
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/collectionproduct/'.$id.'.cache';

		$result=true;

		if(!file_exists($savePath))
		{
			$result=false;
		}

		return $result;
	}

	public static function loadCache($listHash='')
	{
		$savePath=ROOT_PATH.'contents/fastecommerce/collectionproduct/'.$listHash.'.cache';

		$result=false;

		$loadData=false;

		if(file_exists($savePath))
		{
			$result=unserialize(file_get_contents($savePath));

			$userid=(int)$result['userid'];

			$products=$result['product'];

			$total=count($products);

			$loadData=array();

			for ($i=0; $i < $total; $i++) { 
				$theID=$products[$i];

				$prodData=Products::loadCache($theID);

				if(!$prodData)
				{
					continue;
				}

				$loadData[]=$prodData;
			}

			if($userid>0)
			{
				Cookie::make('affiliateid',$userid,1440*30);
			}
		}
		
		return $loadData;
	}

	public static function url($colHash='')
	{
		$url=System::getUrl().'collection/'.$colHash;

		return $url;
	}

	public static function saveCache($userid=1,$listID='')
	{
		if(!preg_match_all('/(\d+)/i', $listID, $matches))
		{
			return false;
		}

		$inputData=$matches[1];

		sort($inputData);

		$listID=implode(',', $inputData);

		$listHash=md5(trim($listID));

		$savePath=ROOT_PATH.'contents/fastecommerce/collectionproduct/'.$listHash.'.cache';

		if(file_exists($savePath))
		{
			return $listHash;
		}

		$loadUser=Users::exists($userid);

		if(!$loadUser)
		{
			$userid=0;
		}

		$total=count($inputData);

		$result=array();

		for ($i=0; $i < $total; $i++) { 
			$theID=$inputData[$i];

			$loadProd=Products::exists($theID);

			if($loadProd==true)
			{
				$result[]=$theID;
			}
		}

		$insertData=array(
			'userid'=>$userid,
			'product'=>$result
			);

		File::create($savePath,serialize($insertData));	

		return $listHash;
			
	}

	public static function cachePath()
	{
		$result=ROOT_PATH.'application/caches/dbcache/system/collections_products/';

		return $result;
	}	


}