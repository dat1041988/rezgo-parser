<?

	// send the user home if they shouldn't be here
	if(!$trans_num) $site->sendTo($site->base."/order-not-found:empty");
	
	// start a session so we can grab the analytics code
	session_start();
	
	$order_bookings = $site->getBookings('t=order_code&q='.$trans_num);

	if(!$order_bookings) { $site->sendTo("/order-not-found:".$_REQUEST['trans_num']); }
	
	// check and see if we want to be here or on the individual item
	// if we only have 1 item and the cart is off, forward them through
	if(!$site->getCartState() && count($order_bookings) == 1) {
		$site->sendTo($site->base.'/complete/'.$site->encode($order_bookings[0]->trans_num).'/print');
	}
	
	$company = $site->getCompanyDetails();

	$rzg_payment_method = 'None';
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Booking - <?=$trans_num?></title>
	
	<!-- Bootstrap CSS -->
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css" rel="stylesheet">
	
	<!-- Font awesome --> 
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<!--[if IE 7]><link href="<?=$this->path?>/css/font-awesome-ie7.css" rel="stylesheet"><![endif]-->

	
	<!-- Rezgo stylesheet -->
	<link href="<?=$site->path?>/css/rezgo.css" rel="stylesheet">
	
	<? if($site->exists($site->getStyles())) { ?>
		<style><?=$site->getStyles();?></style>
	<? } ?>
		
	<!-- jQuery & Bootstrap JS -->
	<script src="<?=$site->base?>/js/iframeResizer.contentWindow.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="<?=$site->path?>/js/bootstrap.min.js"></script>
	
</head>
<body>
	<div class="container-fluid rezgo-container">
		<h2 id="rezgo-order-head">Your order <?=$trans_num?> contains <?=count($order_bookings)?> booking<?=((count($order_bookings) != 1) ? 's' : '')?></h2>

		<? $n = 1; ?>

		<? foreach( $order_bookings as $booking ) { ?>

			<? 
			$item = $site->getTours('t=uid&q='.$booking->item_id, 0); 
			$share_url = urlencode('http://'.$_SERVER['HTTP_HOST'].$site->base.'/details/'.$item->com.'/'.$site->seoEncode($item->item));
			?>

			<? $site->readItem($booking); ?>

			<div class="row rezgo-form-group rezgo-confirmation"> 
				<div class="clearfix"></div>

				<h3><?=$booking->tour_name?>&nbsp;(<?=$booking->option_name?>)</h3>

				<div class="col-md-4 col-sm-12">
					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr>
							<td class="rezgo-td-label">Transaction&nbsp;#:</td>
							<td class="rezgo-td-data"><?=$booking->trans_num?></td>
						</tr>

						<? if ((string) $booking->date != 'open') { ?>
							<tr>
								<td class="rezgo-td-label">Date:</td>
								<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->date)?>
								<? if ($booking->time != '') { ?> at <?=$booking->time?><? } ?>
								</td>
							</tr>
						<? } else { ?>
							<? if ($booking->time) { ?>
								<tr id="rezgo-receipt-booked-for">
									<td class="rezgo-td-label"><span>Time:</span></td>
									<td class="rezgo-td-data"><span><?=$booking->time?></span></td>
								</tr>
							<? } ?>
						<? } ?>

						<? if (isset($booking->expiry)) { ?>
							<tr>
								<td class="rezgo-td-label">Expires:</td>
								<? if ((int) $booking->expiry !== 0) { ?>
								<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->expiry)?>
								<? } else { ?>
								<td class="rezgo-td-data">Never
								<? } ?>
								</td>
							</tr>
						<? } ?>

						<tr>
							<td class="rezgo-td-label">Booking Status:</td>
							<td class="rezgo-td-data"><?=(($booking->status == 1) ? 'CONFIRMED' : '')?><?=(($booking->status == 2) ? 'PENDING' : '')?><?=(($booking->status == 3) ? 'CANCELLED' : '')?></td>
						</tr>

						<? if($site->exists($booking->trigger_code)) { ?>
							<tr>
								<td class="rezgo-td-label" class="rezgo-promo-label"><span>Promotional&nbsp;Code:</span></td>
								<td class="rezgo-td-data"><?=$booking->trigger_code?></td>
							</tr>
						<? } ?>
					</table>
				</div>

				<div class="col-md-8 col-sm-12">
					<table class="table table-responsive table-bordered table-striped rezgo-billing-cart">
						<tr>
							<td class="text-right"><label>Type</label></td>
							<td class="text-right"><label class="hidden-xs">Qty.</label></td>
							<td class="text-right"><label>Cost</label></td>
							<td class="text-right"><label>Total</label></td>
						</tr>

						<? foreach( $site->getBookingPrices() as $price ): ?>
							<tr>
								<td class="text-right"><?=$price->label?></td>
								<td class="text-right"><?=$price->number?></td>
								<td class="text-right">
								<? if($site->exists($price->base)) { ?>
									<span class="discount"><?=$site->formatCurrency($price->base)?></span>
								<? } ?>
								&nbsp;<?=$site->formatCurrency($price->price)?></td>
								<td class="text-right"><?=$site->formatCurrency($price->total)?></td>
							</tr>
						<? endforeach; ?>

						<tr>
							<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
							<td class="text-right"><?=$site->formatCurrency($booking->sub_total)?></td>
						</tr>

						<? foreach( $site->getBookingLineItems() as $line ) { ?>
							<? 
							unset($label_add);

							if($site->exists($line->percent) || $site->exists($line->multi)) {
								$label_add = ' (';

								if($site->exists($line->percent)) $label_add .= $line->percent.'%';

								if($site->exists($line->multi)) {
									if(!$site->exists($line->percent)) $label_add .= $site->formatCurrency($line->multi);

									$label_add .= ' x '.$booking->pax;
								}

								$label_add .= ')';	
							}
							?>

							<? if( $site->exists($line->amount) ) { ?>
								<tr>
									<td colspan="3" class="text-right"><strong><?=$line->label?><?=$label_add?></strong></td>
									<td class="text-right"><?=$site->formatCurrency($line->amount)?></td>
								</tr>
							<? } ?>
						<? } ?>

						<? foreach($site->getBookingFees() as $fee){ ?>
							<? if($site->exists($fee->total_amount)){ ?>
								<tr>
									<td colspan="3" class="text-right"><strong><?=$fee->label?></strong></td>
									<td class="text-right"><?=$site->formatCurrency($fee->total_amount)?></td>
								</tr>
							<? } ?>
						<? } ?>

						<tr>
							<td colspan="3" class="text-right"><strong>Total</strong></td>
							<td class="text-right"><strong><?=$site->formatCurrency($booking->overall_total)?></strong></td>
						</tr>

						<? if($site->exists($booking->deposit)) { ?>
							<tr>
								<td colspan="3" class="text-right"><strong>Deposit</strong></td>
								<td class="text-right"><strong><?=$site->formatCurrency($booking->deposit)?></strong></td>
							</tr>
						<? } ?>

						<? if($site->exists($booking->overall_paid)) { ?>
							<tr>
								<td colspan="3" class="text-right"><strong>Total Paid</strong></td>
								<td class="text-right"><strong><?=$site->formatCurrency($booking->overall_paid)?></strong></td>
							</tr>

							<tr>
								<td colspan="3" class="text-right"><strong>Total&nbsp;Owing</strong></td>
								<td class="text-right"><strong><?=$site->formatCurrency(((float)$booking->overall_total - (float)$booking->overall_paid))?></strong></td>
							</tr>
						<? } ?>
					</table>
				</div>
			</div>

			<!-- //	tour confirm --> 
	
			<div style="page-break-after:always;"></div>

			<? 
			$cart_total += ((float)$booking->overall_total); 
			$cart_owing += ((float)$booking->overall_total - (float)$booking->overall_paid); 

			if($booking->payment_method != 'None') {
				$rzg_payment_method = $booking->payment_method;
			} 
			?>
		<? } ?>

		<div class="rezgo-content-row" id="rezgo-order-billing-info">
			<h2 id="rezgo-order-billing-info">Billing Information</h2>

			<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
				<tr>
					<td class="rezgo-td-label">Name:</td>
					<td class="rezgo-td-data"><?=$booking->first_name?> <?=$booking->last_name?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Address:</td>
					<td class="rezgo-td-data"><?=$booking->address_1?><? if($site->exists($booking->address_2)) { ?>, <?=$booking->address_2?><? } ?><? if($site->exists($booking->city)) { ?>, <?=$booking->city?><? } ?><? if($site->exists($booking->stateprov)) { ?>, <?=$booking->stateprov?><? } ?><? if($site->exists($booking->postal_code)) { ?>, <?=$booking->postal_code?><? } ?>, <?=$site->countryName($booking->country)?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Phone&nbsp;No.:</td>
					<td class="rezgo-td-data"><?=$booking->phone_number?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Email:</td>
					<td class="rezgo-td-data"><?=$booking->email_address?></td>
				</tr>
			</table>
		</div>

		<div class="rezgo-content-row" id="rezgo-order-payment-info">
			<h2 id="rezgo-order-payment-head">Your Payment Information</h2>
	
			<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
				<tr>
					<td class="rezgo-td-label">Total&nbsp;Order:</td>
					<td class="rezgo-td-data"><?=$site->formatCurrency($cart_total)?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Total&nbsp;Owing:</td>
					<td class="rezgo-td-data"><?=$site->formatCurrency($cart_owing)?></td>
				</tr>
				<? if ($cart_total > 0) { ?>
					<tr>
						<td class="rezgo-td-label">Payment&nbsp;Method:</td>
						<td class="rezgo-td-data"><?=$rzg_payment_method?></td>
					</tr>
				<? } ?>
			</table>		
	
		</div>

		<div class="rezgo-content-row" id="rezgo-order-company-info">
			<h2 id="rezgo-order-company-head">Service Provided By</h2>
	
			<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
				<tr>
					<td class="rezgo-td-label">Company:</td>
					<td class="rezgo-td-data"><?=$company->company_name?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Address:</td>
					<td class="rezgo-td-data"><?=$company->address_1?> <?=$company->address_2?><br />
					<?=$company->city?>, <? if($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?><?=$site->countryName($company->country)?><br />
					<?=$company->postal_code?>
					</td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Phone:</td>
					<td class="rezgo-td-data"><?=$company->phone?></td>
				</tr>
				<tr>
					<td class="rezgo-td-label">Email:</td>
					<td class="rezgo-td-data"><?=$company->email?></td>
				</tr>
				<? if($site->exists($company->tax_id)) { ?>
					<tr>
						<td class="rezgo-td-label">Tax ID:</td>
						<td class="rezgo-td-data"><?=$company->tax_id?></td>
					</tr>
				<? } ?>
			</table>
		</div>
	</div>
</body>
</html>