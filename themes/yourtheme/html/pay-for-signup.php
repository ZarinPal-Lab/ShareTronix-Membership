<?php
	
	$this->load_template('header.php');
	
?>


	
	<style>
	
	input[type=submit].zarin_pfs{
	
	font-size:15px;
	font-weight:bold;
	font-family:tahoma;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	padding:10px;
	cursor:pointer;
	background-color:#ffeb0d;
	
	border:solid 1px #ffc820;
	}
	
	</style>

	<? if($D->error){
	echo errorbox('خطا',$D->error_message);
	} elseif($D->submit){
	echo okbox('خطا',$D->ok_message);
	}
	
	?>
	
<div align="center" style="width:100%;height:500px;background: url('<?= $C->SITE_URL.'themes/'.$C->THEME.'/imgs/Credit-cards.jpg'?>') no-repeat center">
<?if($this->user->info->pay_for_signup){?>
<?= okbox("سلام:","شما قبلا حق عضویت پرداخت کردید...",false);?>
<?php
	
	$this->load_template('footer.php');
	exit;
?>

<?}?>
<?= msgbox("توضیحات:","برای ادامه فعالیت در این مجموعه مستلزم پرداخت حق عضویت به مقدار ".$D->pfs." تومان می باشید <br>",false);?>

<form action="<?=$C->SITE_URL?>pay-for-signup" method="post">

<input type="hidden"  name="PFS_HI" value="<?= $D->rand_input ?>"/>
<input type="submit" name="OK" class="zarin_pfs" value="پرداخت حق عضویت از درگاه زرین پال"/>

</form>
</div>	
	

	
	
	
	
	
	
	
<?php
	
	$this->load_template('footer.php');
	
?>