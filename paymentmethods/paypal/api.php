<?php

// http://site.com/paymentapi/paypal/success

class SelfApi
{
	public static function route()
	{
		$listRoute=array(
            'notify'=>'notifyProcess',
            'success'=>'successProcess',
            'error'=>'errorProcess',
            'cancel'=>'cancelProcess',
            'pending'=>'pendingProcess',
            'approved'=>'approvedProcess',
            'completed'=>'completedProcess',
            'oneclick_notify'=>'oneClickNotify',
            'oneclick_cancel'=>'oneClickCancel',
			);

		return $listRoute;
	}

    public static function oneClickCancel()
    {

        Redirects::to(System::getUrl());

    }

    public static function cancelProcess()
    {
        $orderid=trim(Request::get('custom',0));

        Orders::update($orderid,array(
            'status'=>'canceled'
            ));

        Orders::saveCache($orderid);

        Redirects::to(System::getUrl());

    }

    public static function notifyProcess()
    {
        $orderid=trim(Request::get('custom',0));

        if((int)$orderid > 0)
        {
            $orderData=Orders::loadCache($orderid);

            if(!$orderData)
            {
                throw new Exception('This order not exists in our system');
                
            }

            // if($orderData['status']!='draft')
            // {
            //     throw new Exception('This order have been verified.');
                
            // }

            $request = 'cmd=_notify-validate';

            foreach ($_POST as $key => $value) {
                $request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
            }            

            $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            $order_status='pending';

            if (!$response) {
                throw new Exception('Failed to verify');
                
            }

            if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($_POST['payment_status'])) 
            {
                switch($_POST['payment_status']) {
                    case 'Canceled_Reversal':
                        $order_status='canceled';
                        break;
                    case 'Completed':
                        $order_status='completed';
                        break;
                    case 'Denied':
                        $order_status='canceled';
                        break;
                    case 'Expired':
                        $order_status='canceled';
                        break;
                    case 'Failed':
                        $order_status='canceled';
                        break;
                    case 'Pending':
                        $order_status='pending';
                        break;
                    case 'Processed':
                        $order_status='pending';
                        break;
                    case 'Refunded':
                        $order_status='refund';
                        break;
                    case 'Reversed':
                        $order_status='canceled';
                        break;
                    case 'Voided':
                        $order_status='pending';
                        break;
                }
            }

            curl_close($curl);

            Orders::update($orderid,array(
                'status'=>$order_status
                ));

            Orders::saveCache($orderid);       

            switch ($order_status) {
                     case 'canceled':
                         Notifies::sendOrderCanceledEmail($orderid);  
                         break;
                     case 'completed':
                         Notifies::sendOrderConfirmationEmail($orderid);  
                         break;
                     case 'refund':
                         Notifies::sendOrderRefundEmail($orderid);  
                         break;
                     
                 }     

        }   
        else
        {
            throw new Exception('Data not valid');
            
        }

        Redirects::to(System::getUrl());

    }

    
    public static function oneClickNotify()
    {
        $productid=trim(Request::get('custom',0));

        $quantity = isset($_POST['item_number'])?$_POST['item_number']:1;

        if((int)$productid > 0)
        {
            $prodData=Products::loadCache($productid);

            if(!$prodData)
            {
                throw new Exception('This order not exists in our system');
                
            }

            // if($orderData['status']!='draft')
            // {
            //     throw new Exception('This order have been verified.');
                
            // }

            $request = 'cmd=_notify-validate';

            foreach ($_POST as $key => $value) {
                $request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
            }            

            $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            $order_status='pending';

            if (!$response) {
                throw new Exception('Failed to verify');
                
            }

            if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($_POST['payment_status'])) 
            {
                switch($_POST['payment_status']) {
                    case 'Canceled_Reversal':
                        $order_status='canceled';
                        break;
                    case 'Completed':
                        $order_status='completed';
                        break;
                    case 'Denied':
                        $order_status='canceled';
                        break;
                    case 'Expired':
                        $order_status='canceled';
                        break;
                    case 'Failed':
                        $order_status='canceled';
                        break;
                    case 'Pending':
                        $order_status='pending';
                        break;
                    case 'Processed':
                        $order_status='pending';
                        break;
                    case 'Refunded':
                        $order_status='refund';
                        break;
                    case 'Reversed':
                        $order_status='canceled';
                        break;
                    case 'Voided':
                        $order_status='pending';
                        break;
                }
            }

            curl_close($curl);

            Orders::update($orderid,array(
                'status'=>$order_status
                ));

            Orders::saveCache($orderid);       

            switch ($order_status) {
                     case 'completed':
                         $userid=Users::getCookieUserId();

                         $orderData=array();

                         $userData=array();

                         if((int)$userid > 0)
                         {
                            $userData=Users::loadCache($userid);
                         }
                         else
                         {
                            $username=String::randNumber(10);

                            $password=String::randAlpha(10);

                            $email=String::randNumber(10).'@gmail.com';

                            $payer_firstname = isset($_POST['first_name'])?$_POST['first_name']:'Your';

                            $payer_lastname = isset($_POST['last_name'])?$_POST['last_name']:'name';                            

                            if(isset($_POST['payer_email']))
                            {
                                $email=trim($_POST['payer_email']);
                            }

                            $userid=Users::insert(array(
                                'username'=>$username,
                                'password'=>String::encrypt($password),
                                'email'=>$email,
                                'groupid'=>System::$setting['default_member_groupid']
                                ));

                            if(!$userid)
                            {
                                throw new Exception('Data not valid');
                            }

                            Address::insert(array(
                                'userid'=>$userid,
                                'firstname'=>$payer_firstname,
                                'lastname'=>$payer_lastname,
                                ));

                            Users::makeLogin($username,$password);

                            $userData=Users::loadCache($userid);
                         }

                         $orderData['userid']=$userid;

                         $orderData['shipping_firstname']=$payer_firstname;

                         $orderData['shipping_lastname']=$payer_lastname;

                         $orderData['ip']='';

                         $orderData['status']='completed';

                         $orderData['vat']=FastEcommerce::getVAT();

                         $orderData['affiliateid']=Affiliates::getAffiliateID();

                         $totalnovat=(double)$prodData['sale_price']*(int)$quantity;

                         $orderData['before_vat']=$totalnovat;

                         $totalVat=((double)$totalnovat*(double)$orderData['vat'])/100;

                         $totalMoney=(double)$totalnovat+(double)$totalVat;

                         $orderData['total']=$totalMoney;

                         $orderData['summary']=array();

                         $orderData['summary']['payment_method']='Paypal';

                         $orderData['summary']['totalnovat']=$totalnovat;

                        $orderData['summary']['weight']='0 '.FastEcommerce::getWeightUnit();

                        $orderData['summary']['vat']=$orderData['vat'];

                        $orderData['summary']['totalvat']=$totalVat;

                        $orderData['summary']['total']=$totalMoney;

                        $orderData['summary']['total_product']=$quantity;

                        $orderData['summary']['totalusecoupon']=0;

                        $orderData['summary']['totalFormat']=FastEcommerce::money_format($totalMoney);

                        $orderData['summary']['shipping_fee']=0;

                        $orderData['summary']['tax']=0;

                        $orderData['summary']['shipping_method']='';

                        $orderData['summary']['shipping_amount']='';

                        $orderData['summary']['cart_product']=array(
                            $productid=>$prodData
                            );

                        $orderData['summary']['coupon']='';

                         $orderid=Orders::insert($orderData);

                         if($orderid)
                         {
                            OrderProducts::insert(array(
                                'orderid'=>$orderid,
                                'userid'=>$userid,
                                'productid'=>$productid,
                                'quantity'=>$quantity,
                                'price'=>$prodData['sale_price'],
                                'total'=>$orderData['total'],
                                ));

                            Orders::saveCache($orderid);
                         }


                         break;
                     
                 }     

        }   
        else
        {
            throw new Exception('Data not valid');
            
        }

        Redirects::to(System::getUrl());

    }

	
}
