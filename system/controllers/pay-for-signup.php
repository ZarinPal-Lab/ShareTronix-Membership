<?php
	/*
	ALTER TABLE  `users` ADD  `pay_for_signup` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `is_network_admin`
	*/
	if( !$this->network->id ) {
		$this->redirect('home');
	}
		if( !$this->user->is_logged ) {
		$this->redirect('signup');
	}
	include_once('helpers/lib/nusoap.php');
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/user.php');
	$D->error=false;
	$D->error_message = "";
	$D->submit = false;
$D->ok_message = "";
	
	$D->pfs = 1000;  //به تومان وارد کنید///
	
	$D->rand_input = md5(rand(0,10000).time());
	$this->user->ses["RAND_KEY_PFS"] = $D->rand_input;
	
	if(isset($_POST["OK"]) ){
	
	 $MerchantID = "52459b41-0100-4130-8233-3acb5ee8a9d4";
	$Amount = ($D->pfs); //Amount will be based on Toman  - Required
	$Description = 'حق عضویت - '.$C->SITE_TITLE;  // Required
	$Email = $this->user->info->email; // Optional
	$Mobile =''; // Optional
	$CallbackURL = $C->SITE_URL.'pay-for-signup/tab:payed/'; // Required
	
	
	// URL also Can be https://ir.zarinpal.com/pg/services/WebGate/wsdl
	$client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl'); 
	$client->soap_defencoding = 'UTF-8';
	$result = $client->call('PaymentRequest', array(
													array(
															'MerchantID' 	=> $MerchantID,
															'Amount' 		=> $Amount,
															'Description' 	=> $Description,
															'Email' 		=> $Email,
															'Mobile' 		=> $Mobile,
															'CallbackURL' 	=> $CallbackURL
														)
													)
	);

	//Redirect to URL You can do it also by creating a form
	if($result['Status'] == 100)
	{
	unset($_POST);
$this->user->sess['PAY_FOR_SIGNUP'] = $result['Authority'];
$this->redirect('https://www.zarinpal.com/pg/StartPay/'.$result['Authority']);
		
	} else {
		unset($_POST);
		$D->error=true;
		$D->error_message = "خطا در چرداخت...".$result['Status'];
		$this->load_template('pay-for-signup.php');
		exit;
	}
	
	
	}
if($this->param('tab')=="payed" && isset($_GET['Authority']) && isset($this->user->sess['PAY_FOR_SIGNUP']) && $_GET['Authority'] == $this->user->sess['PAY_FOR_SIGNUP']){

$MerchantID = "52459b41-0100-4130-8233-3acb5ee8a9d4";
$Authority = $this->user->sess['PAY_FOR_SIGNUP'];
$au =$Authority;
$Amount = $D->pfs;

$client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl'); 
		$client->soap_defencoding = 'UTF-8';
		$result = $client->call('PaymentVerification', array(
															array(
																	'MerchantID'	 => $MerchantID,
																	'Authority' 	 => $Authority,
																	'Amount'	 	 => $Amount
																)
															)
		);
		
		



if(trim($result['Status']) !== '100'){
		unset($_POST);
		unset($_GET);
		unset($this->user->sess['PAY_FOR_SIGNUP']);	
		$D->error=true;
		$D->error_message = "خطا در پرداخت ".$result['Status'];
		$this->load_template('pay-for-signup.php');
		exit;
		}



$trak = $result['RefID'];

unset($_POST);
unset($_GET);
unset($this->user->sess['PAY_FOR_SIGNUP']);
$db2->query('UPDATE users SET pay_for_signup = "1" WHERE id="'.$this->user->info->id.'" LIMIT 1');
$this->network->get_user_by_id($this->user->info->id,true);
$D->submit = true;
$D->ok_message = "پرداخت با شماره تراکنش ".$trak." انجام شد.";

}	
	
	
	
	$this->load_template('pay-for-signup.php');
	
?>