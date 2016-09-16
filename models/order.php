<?php


function adminActionProcess()
{
	$action=Request::get('action');

	$id=Request::get('id');

	$listID="'".implode("','",$id)."'";

	switch ($action) {
		case 'delete':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData)
					{
						unset($id[$i]);
					}

				}

				sort($id);
			}

			Orders::remove($id," id IN ($listID)");

			OrderProducts::remove($id," orderid IN ($listID)");

			break;
		case 'pending':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData)
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'pending'
							)," id='$theID'");

						Orders::saveCache($theID);						
					}

				}
			}



			break;
		case 'shipping':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData || $loadData['status']=='shipping')
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'shipping'
							)," id='$theID'");

						Orders::saveCache($theID);	

						Notifies::sendShippingConfirmationEmail($theID);						

					}

				}
			}



			break;
		case 'approved':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData || $loadData['status']=='approved')
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'approved'
							)," id='$theID'");

						Orders::saveCache($theID);		

						if(is_array($loadData['products']))
						{
							$listID=array_keys($loadData['products']);

							$totalID=count($listID);

							if($totalID > 0)
							{
								for ($j=0; $j < $totalID; $j++) { 
									$prodID=$listID[$j];

									Products::up("where id='$prodID'",'orders',1);

									Products::saveCache($prodID);
								}
							}
						}

						Notifies::sendOrderConfirmationEmail($theID);						

					}

				}
			}



			break;
		case 'canceled':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData || $loadData['status']=='canceled')
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'canceled'
							)," id='$theID'");

						Orders::saveCache($theID);

						Notifies::sendOrderCanceledEmail($theID);						
					}

				}
			}



			break;
		case 'refund':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData || $loadData['status']=='refund')
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'refund'
							)," id='$theID'");

						Orders::saveCache($theID);		
								
						Notifies::sendOrderRefundEmail($theID);						

					}

				}
			}



			break;
		case 'completed':

			$owner=Usergroups::getPermission(Users::getCookieGroupId(),'is_fastecommerce_owner');

			if($owner=='yes')
			{

				$total=count($id);

				$userid=Users::getCookieUserId();

				for ($i=0; $i < $total; $i++) { 

					$theID=$id[$i];

					$loadData=Orders::loadCache($theID);

					if(!$loadData || $loadData['status']=='completed')
					{
						unset($id[$i]);
					}
					else
					{
						Orders::update($id,array(
							'status'=>'completed'
							)," id='$theID'");

						Orders::saveCache($theID);		

        				Affiliates::after_insert_order($theID);										
					}

				}
			}



			break;


	}
}
