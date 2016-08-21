<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 ">
    	<form action="" method="post" enctype="multipart/form-data">
    	<input type="hidden" name="id[]" value="<?php echo $orderData['id'];?>">
    		<!-- row -->
    		<div class="row margin-bottom-30">
    			<div class="col-lg-6 ">
                    <div class="input-group input-group-sm">
                        <select class="form-control" name="action">
                            <option value="delete">Delete</option>
                            <option value="pending">Set as Pending</option>
                            <option value="shipping">Set as Shipping</option>
                            <option value="approved">Set as Approved</option>
                            <option value="canceled">Set as Canceled</option>
                            <option value="refund">Set as Refund</option>
                            <option value="completed">Set as Completed</option>
                        </select>
                       <span class="input-group-btn">
                        <button class="btn btn-primary" name="btnAction" type="submit">Apply</button>
                      </span>

                    </div><!-- /input-group -->   				
    			</div>
    		</div>
    		<!-- row -->
    	</form>		
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 text-right">
		<a href="<?php echo System::getUrl();?>admincp/plugins/privatecontroller/fastecommerce/order/sendemail/<?php echo $orderData['id'];?>" class="btn btn-primary"><span class="glyphicon glyphicon-comment"></span> Send Email</a>
	</div>
</div>



<?php

$status='<span class="text-warning">Pending</span>';

if($orderData['status']=='approved')
{
	$status='<span class="text-success">Approved</span>';
}
elseif($orderData['status']=='shipping')
{
	$status='<span class="text-primary">Shipping</span>';
}
elseif($orderData['status']=='canceled')
{
	$status='<span class="text-default">Canceled</span>';
}
elseif($orderData['status']=='refund')
{
	$status='<span class="text-danger">Refund</span>';
}
elseif($orderData['status']=='completed')
{
	$status='<span class="text-success">Completed</span>';
}
elseif($orderData['status']=='draft')
{
	$status='<span class="text-default" style="color:#999;">Draft</span>';
}

?>

<div class="panel panel-default">

  <div class="panel-body">
    <div class="row">
    	<div class="col-lg-12">
    	<h3>Order #<?php echo $orderData['id'];?> - <?php echo $status;?></h3>
    	<span>Date: <?php echo date('M d, Y H:i',strtotime($orderData['date_added']));?></span>
    	<hr>
		    <div class="row">
		    	<div class="col-lg-3 col-md-3 col-sm-3 text-center">
		    		<div class="text-primary" style="font-size:18px;"><?php echo FastEcommerce::money_format($orderData['total']);?></div>
		    		<span>Total</span>
		    	</div>
		    	<div class="col-lg-3 col-md-3 col-sm-3 text-center">
		    		<div class="text-success" style="font-size:18px;"><?php if(isset($orderData['summary']['vat']))echo $orderData['summary']['vat'];else echo '0';?>%</div>
		    		<span>VAT</span>
		    	</div>
		    	<div class="col-lg-3 col-md-3 col-sm-3 text-center">
		    		<div class="text-info" style="font-size:18px;"><?php echo $orderData['summary']['shipping_method'].' '.FastEcommerce::money_format($orderData['summary']['shipping_amount']);?></div>
		    		<span>Shipping Method</span>
		    	</div>
		    	<div class="col-lg-3 col-md-3 col-sm-3 text-center">
		    		<div class="text-warning" style="font-size:18px;"><?php echo $orderData['summary']['payment_method'];?></div>
		    		<span>Payment Method</span>
		    	</div>

		    </div>   
		<h3>Billing</h3>    
		<hr>
		<p>
		<strong>Fullname: <?php echo $billing['firstname'].' '.$billing['lastname'];?></strong>	
		</p>

		<p>
		<span class="text-danger">Phone: <?php echo $billing['phone'];?></span>
		</p>
		<p>
		<span class="">Email: <?php echo $billing['email'];?></span>
		</p>

		<p>
		<span class="text-info">Company: <?php echo $billing['company'];?></span>
		</p>

		<p>
		<span class="text-primary">Address: <?php echo $billing['address_1'];?>, <?php echo $billing['address_2'];?>, <?php echo $billing['city'];?>, <?php echo $billing['postcode'];?>, <?php echo $billing['state'];?>, <?php echo $billing['country'];?> </span>
		</p>
		<hr>
		<h3>Shipping to</h3>    
		<hr>
		<p>
		<strong>Fullname: <?php echo $orderData['shipping_firstname'].' '.$orderData['shipping_lastname'];?></strong>	
		</p>

		<p>
		<span class="text-danger">Phone: <?php echo $orderData['shipping_phone'];?></span>
		</p>

		<p>
		<span class="text-info">Company: <?php echo $orderData['shipping_company'];?></span>
		</p>

		<p>
		<span class="text-primary">Address: <?php echo $orderData['shipping_address1'];?>, <?php echo $orderData['shipping_address2'];?>, <?php echo $orderData['shipping_city'];?>, <?php echo $orderData['shipping_postcode'];?>, <?php echo $orderData['shipping_state'];?>, <?php echo $orderData['shipping_country'];?> </span>
		</p>

		<h3>Comments</h3>    
		<hr>
		<span><?php echo $orderData['comment'];?></span>	

    	</div>
    </div>
  </div>
</div>

<!-- row -->
<div class="row">
	<div class="col-lg-12">
	
		<div class="panel panel-default">

		  <div class="panel-body">
		    <div class="row">
		    	<div class="col-lg-12">
				<h3>Products</h3>
				<hr> 	

				<table class="table table-hover">
					<thead>
						<tr>
							<td class="col-lg-5 col-md-5 col-sm-5 "><strong>Title</strong></td>
							<td class="col-lg-2 col-md-2 col-sm-2 "><strong>Price</strong></td>
							<td class="col-lg-2 col-md-2 col-sm-2 "><strong>Quantity</strong></td>
							<td class="col-lg-3 col-md-3 col-sm-3 text-right"><strong>Total</strong></td>
						</tr>
					</thead>

					<tbody>
					<?php

					if(isset($orderData['summary']['cart_product'][0]['id']))
					{
						$li='';

						$total=count($orderData['summary']['cart_product']);

						$status='';

						for ($i=0; $i < $total; $i++) { 

							$prodID=$orderData['summary']['cart_product'][$i]['id'];

							$attr=isset($orderData['summary']['cart_product'][$i]['attr'])?$orderData['summary']['cart_product'][$i]['attr']:'';

							$li.='
							<tr>
								<td class="col-lg-5 col-md-5 col-sm-5 "><a href="'.$orderData['summary']['cart_product'][$i]['url'].'" target="_blank"><strong>'.$orderData['summary']['cart_product'][$i]['title'].'</strong></a>
								<br>
								<span style="color:#999;">'.$attr.'</span>
								</td>
								<td class="col-lg-2 col-md-2 col-sm-2 "><strong class="text-success">'.FastEcommerce::money_format($orderData['summary']['cart_product'][$i]['price']).'</strong></td>
								<td class="col-lg-2 col-md-2 col-sm-2 ">'.number_format($orderData['summary']['cart_product'][$i]['quantity']).'</td>
								<td class="col-lg-3 col-md-3 col-sm-3  text-right"><strong class="text-success">'.FastEcommerce::money_format($orderData['summary']['cart_product'][$i]['total']).'</strong></td>
							</tr>

							';

							if($orderData['summary']=='completed' && isset($orderData['products'][0]))
							{
								$li.='
									<tr>
										<td colspan="3" class="col-lg-12">
										<span class="glyphicon glyphicon-cloud-download" style="color:#999;"></span> <a href="'.$orderData['products'][0].'"><span>Click here to download file of product.</span></a>
										</td>
									</tr>
									';
							}

						}

						echo $li;
					}
					?>						
					</tbody>
				</table>
		    	</div>
		    </div>
		  </div>
		</div>				
	</div>
</div> 
<!-- row -->