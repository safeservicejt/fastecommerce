<?php

Plugins::install('install_fastecommerce');

Plugins::uninstall('uninstall_fastecommerce');

function install_fastecommerce()
{
	Dir::remove(ROOT_PATH.'contents/fastecommerce');
	
	Dir::remove(CACHES_PATH.'dbcache/system/fastecommerce');
	
	Dir::remove(CACHES_PATH.'fastecommerce');

	$pluginPath=dirname(__FILE__).'/';

	$installPath=$pluginPath.'install/';

	$dbFile=$installPath.'db.sql';

	if(file_exists($dbFile))
	{
		Database::import($dbFile,Database::getPrefix());
	}

	File::copyMatch(ROOT_PATH.'contents/plugins/fastecommerce/install/*.php',ROOT_PATH.'includes/');

	// System::saveSetting(array(
	// 	'default_adminpage_method'=>'url',
	// 	'default_adminpage_url'=>'plugins/privatecontroller/fastecommerce/stats'
	// 	));

	CustomPlugins::add('before_system_start',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'before_system_start'
		));

	CustomPlugins::add('before_frontend_start',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'before_frontend_start'
		));

	CustomPlugins::add('before_admincp_start',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'before_admincp_start'
		));


	CustomPlugins::add('after_insert_user',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'after_insert_user'
		));	


	CustomPlugins::add('after_remove_user',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'after_remove_user'
		));	

	CustomPlugins::add('before_register_user',array(
		'method_call'=>'class',
		'path'=>'fly',
		'class'=>'FastEcommerce',
		'func'=>'before_register_user'
		));		


	UserGroups::addPermissionToAll(array(
		'can_control_plugin'=>'yes',
		'can_manage_link'=>'no',
		'is_fastecommerce_owner'=>'no',
		'can_addnew_product'=>'no',
		'can_update_product'=>'no',
		'can_remove_product'=>'no',
		));

	UserGroups::updatePermissionToAll(array(
		'can_change_profile'=>'yes',
		'can_manage_post'=>'no',
		'can_manage_link'=>'no',
		'can_addnew_category'=>'no',
		'can_addnew_redirect'=>'no',
		'can_manage_contactus'=>'no',
		'can_addnew_page'=>'no',
		'can_addnew_user'=>'no',
		'can_addnew_usergroup'=>'no',
		'can_edit_usergroup'=>'no',
		'can_setting_system'=>'no',
		'can_manage_plugins'=>'no',
		'can_manage_themes'=>'no',
		'can_import_theme'=>'no',
		'can_activate_plugin'=>'no',
		'can_uninstall_plugin'=>'no',
		'can_deactivate_plugin'=>'no',
		'can_install_plugin'=>'no',
		'can_import_plugin'=>'no',
		'can_manage_category'=>'no',
		'can_manage_user'=>'no',
		'can_manage_usergroup'=>'no',
		'show_category_manager'=>'no',
		'show_post_manager'=>'no',
		'show_comment_manager'=>'no',
		'show_page_manager'=>'no',
		'show_link_manager'=>'no',
		'show_user_manager'=>'no',
		'show_usergroup_manager'=>'no',
		'show_contact_manager'=>'no',
		'show_theme_manager'=>'no',
		'show_plugin_manager'=>'no',
		'show_setting_manager'=>'no',
		));		

	$groupid=(int)Users::getCookieGroupId();

	UserGroups::updatePermission($groupid,array(
		'is_fastecommerce_owner'=>'yes',
		'can_addnew_product'=>'yes',
		'can_update_product'=>'yes',
		'can_remove_product'=>'yes',		
		'can_manage_post'=>'yes',
		'can_addnew_post'=>'yes',
		'can_manage_link'=>'yes',
		'can_addnew_category'=>'yes',
		'can_addnew_redirect'=>'yes',
		'can_manage_contactus'=>'yes',
		'can_addnew_page'=>'yes',
		'can_addnew_user'=>'yes',
		'can_addnew_usergroup'=>'yes',
		'can_edit_usergroup'=>'yes',
		'can_setting_system'=>'yes',
		'can_manage_plugins'=>'yes',
		'can_manage_themes'=>'yes',
		'can_import_theme'=>'yes',
		'can_activate_plugin'=>'yes',
		'can_uninstall_plugin'=>'yes',
		'can_deactivate_plugin'=>'yes',
		'can_install_plugin'=>'yes',
		'can_import_plugin'=>'yes',
		'can_manage_category'=>'yes',
		'can_manage_user'=>'yes',
		'can_manage_usergroup'=>'yes',
		'show_category_manager'=>'yes',
		'show_post_manager'=>'yes',
		'show_comment_manager'=>'yes',
		'show_page_manager'=>'yes',
		'show_link_manager'=>'yes',
		'show_user_manager'=>'yes',
		'show_usergroup_manager'=>'yes',
		'show_contact_manager'=>'yes',
		'show_theme_manager'=>'yes',
		'show_plugin_manager'=>'yes',
		'show_setting_manager'=>'yes',	
		'can_control_plugin'=>'yes',	
		));		

	    Database::addField('categories','orders',array(
	        'type'=>'INT',
	        'length'=>9,
	        'default'=>0
	    ));	
	    Database::addField('categories','products',array(
	        'type'=>'INT',
	        'length'=>9,
	        'default'=>0
	    ));	
}


function uninstall_fastecommerce()
{

	UserGroups::removePermissionToAll(array(
		'is_fastecommerce_owner',
		'can_addnew_product',	
		));		

	File::exists(ROOT_PATH.'includes/FastEcommerce.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Affiliates.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Products.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Brands.php',function($filePath){
		unlink($filePath);
	});
	
	File::exists(ROOT_PATH.'includes/Cart.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Coupons.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Customers.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Discounts.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Downloads.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/OrderProducts.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Orders.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Payments.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Reviews.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/StoreLogs.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductAttrs.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductBrands.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductDiscounts.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductDownloads.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductImages.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductReviews.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductCategories.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ProductTags.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/EmailTemplates.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Notifies.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/GeoZone.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/TaxRates.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/ShippingRates.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/WishList.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/AffiliatesStats.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/AffiliatesWithdraws.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/CollectionsProducts.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/AffiliatesRanks.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/NewsLetter.php',function($filePath){
		unlink($filePath);
	});

	File::exists(ROOT_PATH.'includes/Vouchers.php',function($filePath){
		unlink($filePath);
	});



	CustomPlugins::removeByClass('FastEcommerce');
	
	$prefix=Database::getPrefix();

	Database::dropTable('coupons',$prefix);

	Database::dropTable('brands',$prefix);

	Database::dropTable('customers',$prefix);

	Database::dropTable('discounts',$prefix);

	Database::dropTable('downloads',$prefix);

	Database::dropTable('orders',$prefix);

	Database::dropTable('order_products',$prefix);

	Database::dropTable('payment_methods',$prefix);

	Database::dropTable('products',$prefix);

	Database::dropTable('product_attrs',$prefix);

	Database::dropTable('product_brands',$prefix);

	Database::dropTable('product_categories',$prefix);

	Database::dropTable('product_discounts',$prefix);

	Database::dropTable('product_downloads',$prefix);

	Database::dropTable('product_images',$prefix);

	Database::dropTable('product_reviews',$prefix);

	Database::dropTable('product_tags',$prefix);

	Database::dropTable('reviews',$prefix);

	Database::dropTable('store_log',$prefix);

	Database::dropTable('user_withdraws',$prefix);

	Database::dropTable('shippingrates',$prefix);

	Database::dropTable('wishlist',$prefix);

	Database::dropTable('affiliate_stats',$prefix);

	Database::dropTable('affiliate_withdraws',$prefix);

	Database::dropTable('collections_products',$prefix);

	Database::dropTable('affiliate_ranks',$prefix);

	Database::dropTable('newsletters',$prefix);

	Database::dropTable('vouchers',$prefix);

    Database::dropField('categories','orders');
    
    Database::dropField('categories','products');

	Dir::remove(ROOT_PATH.'contents/fastecommerce');

}
