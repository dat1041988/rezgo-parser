<? 
$res = $site->getGiftCard($_SESSION['GIFT_CARD_KEY']); 
$card = $res->card;
$billing = $card->billing;
$company = $site->getCompanyDetails();
$site->readItem($company);
$debug = 0;
?>

<div class="container-fluid rezgo-container">
	<div class="row">
		<div class="col-xs-12">
			<div class="rezgo-gift-card-container gift-card-receipt">
				<div class="master-heading">
					<h3 style="margin-bottom:0px;"><span>PURCHASE COMPLETE</span></h3>
				</div>

				<div class="rezgo-gift-card-group balance-section clearfix">
					<div class="heading">
						<h3><span class="text-info">Gift Card Receipt</span></h3>
						<p>Thank you for your gift card purchase.	The gift card has been sent to <span><?=$card->email?>.</span></p>
					</div>
					
					<div class="clearfix">
					<table class="table table-bordered table-striped">
						<tr>
							<td>Date</td>
							<td><?=date((string) $company->date_format, (int) $card->created)?></td>
						</tr>

						<tr>
							<td>Sent To</td>
							<td><?=$card->first_name?> <?=$card->last_name?> <?=(($card->sent->to) ? '('.$card->sent->to.')' : '')?></td>
						</tr>

						<tr>
							<td>Value</td>
							<td><?=$site->formatCurrency((float) $card->amount)?></td>
						</tr>
						<? if((string) $card->sent->message) { ?>
						<tr>
							<td>Message</td>
							<td><?=nl2br((string)$card->sent->message)?></td>
						</tr>
						<? } ?>
					</table>
					</div>
				</div>

				<hr>

				<div class="rezgo-gift-card-group balance-section clearfix">
					<div class="heading">
						<h3><span class="text-info">Billing Information</span></h3>
					</div>

					<div class="clearfix">
					<table class="table table-bordered table-striped">
						<? if ($billing->first_name) { ?>
							<tr>
								<td>FirstName</td>
								<td><?=$billing->first_name?></td>
							</tr>
						<? } ?>

						<? if ($billing->last_name) { ?>
							<tr>
								<td>Last Name</td>
								<td><?=$billing->last_name?></td>
							</tr>
						<? } ?>

						<? if ($billing->address_1) { ?>
							<tr>
								<td>Address</td>
								<td><?=$billing->address_1?></td>
							</tr>
						<? } ?>

						<? if ($billing->address_2) { ?>
							<tr>
								<td>Address 2</td>
								<td><?=$billing->address_2?></td>
							</tr>
						<? } ?>

						<? if ($billing->city) { ?>
							<tr>
								<td>City</td>
								<td><?=$billing->city?></td>
							</tr>
						<? } ?>

						<? if ($billing->state) { ?>
							<tr>
								<td>Prov/State</td>
								<td><?=$billing->state?></td>
							</tr>
						<? } ?>

						<? if ($billing->country) { ?>
							<tr>
								<td>Country</td>
								<td>
									<? foreach ($site->getRegionList() as $iso => $name) { ?>
										<? if ($iso == $billing->country) { ?>
											<?=ucwords($name)?>
										<? } ?>
									<? } ?>
								</td>
							</tr>
						<? } ?>

						<? if ($billing->postal) { ?>
							<tr>
								<td>Postal Code/ZIP</td>
								<td><?=$billing->postal?></td>
							</tr>
						<? } ?>

						<? if ($billing->email) { ?>
							<tr>
								<td>Email</td>
								<td><?=$billing->email?></td>
							</tr>
						<? } ?>

						<? if ($billing->phone) { ?>
							<tr>
								<td>Phone</td>
								<td><?=$billing->phone?></td>
							</tr>
						<? } ?>
					</table>
					</div>

					<div class="rezgo-company-info">
						<p>
							<span>Only one gift card may be used per order.</span>

							<br/>

							<a 
							href="javascript:void(0);"
							onclick="javascript:window.open('/terms',null,'width=800,height=600,status=no,toolbar=no,menubar=no,location=no,scrollbars=1');"
							>Click here to view the terms and conditions.</a>
						</p>

						<br/>

						<h3 id="rezgo-receipt-head-provided-by">
							<span>Valid At</span>
						</h3>

						<address>
							<? $company = $site->getCompanyDetails($booking->cid); ?>
							<strong><?=$company->company_name?></strong><br />
							<?=$company->address_1?><? if($site->exists($company->address_2)) { ?>, <?=$company->address_2?><? } ?>
							<br />
							<?=$company->city?>,
							<? if ($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?>
							<?=$site->countryName($company->country)?><br />
							<?=$company->postal_code?><br />
							<?=$company->phone?><br />
							<?=$company->email?>
							<? if ($site->exists($company->tax_id)) { ?><br />Tax ID: <?=$company->tax_id?><? } ?>
						</address>
					</div>
				</div>
			</div>
		</div>

		<? if ($debug) { ?>
			<div class="col-xs-12">
				<pre><? var_dump($card); ?></pre>
			</div>
		<? } ?>
	</div>	
</div>

<? unset($_SESSION['GIFT_CARD_KEY']); ?>