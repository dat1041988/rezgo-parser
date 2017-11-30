<? 
// send the user home if they shoulden't be here
if (!$_REQUEST['card']) { 
	$site->sendTo($site->base."/gift-card");
}
$company = $site->getCompanyDetails();
$site->readItem($company);
$res = $site->getGiftCard($_REQUEST['card']); 
$card = $res->card;
if (!$card) {
	$site->sendTo($site->base."/gift-not-found");
}
$billing = $card->billing;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Gift Card</title>

	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/font-awesome.min.css" rel="stylesheet">
	<!--[if IE 7]><link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/font-awesome-ie7.css" rel="stylesheet"><![endif]-->
	<link href="<?=$this->path?>/css/rezgo.css" rel="stylesheet">
</head>
<body>
	<div id="rezgo-gift-card-print" class="rezgo-gift-card-container screen-center">
		<div class="row">
			<div class="col-xs-12">
				<p>&nbsp;</p>
			</div>

			<div class="col-xs-12">
				<div class="jumbotron clearfix" style="background-color:#eee !important; border-radius:10px;">
					<div class="row">
						<div class="col-xs-6 col-xxs-12">
							<h5><span class="card-company"><?=ucfirst($company->company_name)?> Gift Card</span></h5>
							<h5><span class="card-company">http://<?=(($company->primary_domain) ? $company->primary_domain : $site->getDomain().'.rezgo.com')?></span></h5>
							<h3><span class="card-amount text-info"><?=$site->formatCurrency((float) $card->amount)?></span></h3>
							<h4><span class="card-number"><?=$site->cardFormat($card->number)?></span></h4>
							<? if ((int) $card->expires !== 0) { ?>
								<h4><span class="card-expiry">
									<span>Expires: </span>
									<span><?=date((string) $company->date_format, (int) $card->expires)?></span>
								</span></h4>
							<? } ?>
							<h4><span class="card-holder-name"><?=ucfirst($card->first_name)?> <?=ucfirst($card->last_name)?></span></h4>
						</div>
						<div class="col-xs-6 col-xxs-12" style="text-align:right">
							<div class="rezgo-voucher-barcode clearfix">
								<img src="/barcode.php?barcode=<?=$card->number?>&width=370&height=180" alt="barcode" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12 screen-center">
				<p>
					<? if (!empty($card->sent->message)) { ?>
						<span><?=htmlspecialchars_decode($card->sent->message)?></span>
						<br>
					<? } ?>
					<? if (!empty($billing->first_name) || !empty($billing->last_name)) { ?>
					<h4><span>From: <?=$billing->first_name?> <?=$billing->last_name?></span></h4>
					<br/>
					<br/>
					<? } ?>
				</p>
			</div>
			
		</div>
		
	</div>
	
	<script>
		window.print();
	</script>
</body>
</html>