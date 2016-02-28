<?php
/*
Plugin Name: Facebook to MemberPress Bridge
Plugin URI: https://wordpress.org/plugins/facebook-membepress-bridge
Description: Enables people who register by Facebook to automatically become members of the Gratishelten plan.
Version: 1.0
Author: Linnea Wilheln
Author URI: http://www.linsoftware.com
*/

/*
Note: This plugin was developed to work with MemberPress Developer Edition version 1.2.6 and
Nextend Facebook Connect version 1.5.7

It creates a MemberPress transaction when a user registers with Facebook.
To adapt this code to your use, you will have to change the product_id.
This plugin is really just for a one-use case, and is probably not very reusable,
but it is a working example of how to extend the functionality of the
Nextend Facebook Connect with the nextend_fb_user_registered hook.
*/
add_action('nextend_fb_user_registered', 'facebookmodifyuser', 10, 3);

function facebookmodifyuser($ID, $user_profile, $facebook) {
	ob_start();
	$oldPost = $_POST;
	$meprt = new MeprTransactionsCtrl();
	$txn       = new MeprTransaction();
	$unique_id = uniqid();
	$nonce = wp_create_nonce( 'memberpress-trans' );
	$user_info = get_userdata($ID);
	$user_login = $user_info->user_login;
	$_POST     = array(
		'trans_num'        => $unique_id,
		'action'           => 'new',
		'_wpnonce'         => $nonce,
		'_wp_http_referer' => '%2Fbeta%2Fwp-admin%2Fadmin.php%3Fpage%3Dmemberpress-trans%26action%3Dnew',
		'user_login'       => $user_login,
		'product_id'       => '4379',
		'amount'           => '0.00',
		'status'           => 'complete',
		'gateway'          => 'Manual',
		'subscr_num'       => '',
		'created_at'       => '',
		'expires_at'       => ''
	);

	$meprt->create_trans( $txn );

	$_POST = $oldPost;
	ob_clean();

}
