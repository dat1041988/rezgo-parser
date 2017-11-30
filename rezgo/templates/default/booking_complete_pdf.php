<?
	// grab and decode the trans_num if it was set
	$trans_num = $site->decode($_REQUEST['trans_num']);
	// send the user home if they shoulden't be here
	if(!$trans_num) $site->sendTo("/".$current_wp_page."/booking-not-found");
	
	$company = $site->getCompanyDetails();
?>		
<html>
<head>
	<style>
	
		body {
			color: #333;
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 14px;
			line-height: 1.42857;
		}
		
		table {
			width:100%;
		}
		
		.table-bordered {
			background: #999999;
		}
		
		.table-bordered td {
			background: #FFFFFF;
		}
		
		.rezgo-td-label {
			font-weight: bold;
			overflow: hidden;
			padding-right: 16px;
			padding-top: 4px;
			text-align: right;
			text-overflow: ellipsis;
			vertical-align: top;
			width: 30%;
		}
		
		.rezgo-td-data {
			width: 70%;
			padding-top: 4px;
		}
		
	</style>
</head>
<body>


	<? if(!$site->getBookings('q='.$trans_num)) $site->sendTo("/booking-not-found:".$_REQUEST['trans_num']); ?>
	
	<? foreach( $site->getBookings('q='.$trans_num) as $booking ): ?>
	
	<? $item = $site->getTours('t=uid&q='.$booking->item_id, 0); ?>
	
	<? $site->readItem($booking) ?>

		<h2>Your Booking (booked on <?=date((string) $company->date_format, (int) $booking->date_purchased_local)?> / local time)</h2>
		
		<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
			<tr>
				<td class="rezgo-td-label" valign="top">Transaction #</td>
				<td class="rezgo-td-data"><?=$booking->trans_num?></td>
			</tr>

			<tr>
				<td class="rezgo-td-label" valign="top">You have booked</td>
				<td class="rezgo-td-data"><?=$booking->tour_name?> &mdash; <?=$booking->option_name?></td>
			</tr>

			<? if((string) $booking->date != 'open') { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Booked For</td>
				<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->date)?>
				<? if($booking->time != '') { ?> at <?=$booking->time?><? } ?>
				</td>
			</tr>
			<? } ?>

			<? if(isset($booking->expiry)) { ?>
			<tr>
				<td class="rezgo-td-label">Expires:</td>
				<? if((int) $booking->expiry !== 0) { ?>
				<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->expiry)?>
				<? } else { ?>
				<td class="rezgo-td-data">Never
				<? } ?>
				</td>
			</tr>
			<? } ?>

			<? if($site->exists($item->duration)) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Duration</td>
				<td class="rezgo-td-data"><?=$item->duration?></td>
			</tr>
			<? } ?>

			<? $location = $item->city . ($site->exists($item->state)) ? ', ' . $item->state : '' . ($site->exists($item->country)) ? ', ' . $site->countryName($item->country) : ''; ?>

			<? if($site->exists($location)) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Location</td>
				<td class="rezgo-td-data"><?=$location?></td>
			</tr>
			<? } ?>

			<? if($site->exists($site->cleanAttr($item->details->pick_up))) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Pickup/Departure Information</td>
				<td class="rezgo-td-data"><?=$site->cleanAttr($item->details->pick_up)?></td>
			</tr>
			<? } ?>

			<? if($site->exists($site->cleanAttr($item->details->drop_off))) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Drop Off/Return Information</td>
				<td class="rezgo-td-data"><?=$site->cleanAttr($item->details->drop_off)?></td>
			</tr>
			<? } ?>

			<? if($site->exists($site->cleanAttr($item->details->bring))) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Things to bring</td>
				<td class="rezgo-td-data"><?=$site->cleanAttr($item->details->bring)?></td>
			</tr>
			<? } ?>

			<? if($site->exists($site->cleanAttr($item->details->itinerary))) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Itinerary</td>
				<td class="rezgo-td-data"><?=$site->cleanAttr($item->details->itinerary)?></td>
			</tr>
			<? } ?>
		</table>
 
		<h2>Payment Information</h2>
		
		<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
			<tr>
				<td class="rezgo-td-label" valign="top">Name</td>
				<td class="rezgo-td-data"><?=$booking->first_name?> <?=$booking->last_name?></td>
			</tr>
			<tr>
				<td class="rezgo-td-label" valign="top">Address</td>
				<td class="rezgo-td-data"><?=$booking->address_1?><? if($site->exists($booking->address_2)) { ?>, <?=$booking->address_2?><? } ?><? if($site->exists($booking->city)) { ?>, <?=$booking->city?><? } ?><? if($site->exists($booking->stateprov)) { ?>, <?=$booking->stateprov?><? } ?><? if($site->exists($booking->postal_code)) { ?>, <?=$booking->postal_code?><? } ?>, <?=$site->countryName($booking->country)?></td>
			</tr>
			<tr>
				<td class="rezgo-td-label" valign="top">Phone Number</td>
				<td class="rezgo-td-data"><?=$booking->phone_number?></td>
			</tr>
			<tr>
				<td class="rezgo-td-label" valign="top">Email Address</td>
				<td class="rezgo-td-data"><?=$booking->email_address?></td>
			</tr>
			<? if($booking->overall_total > 0) { ?>
				<tr>
					<td class="rezgo-td-label" valign="top">Payment Method</td>
					<td class="rezgo-td-data"><?=$booking->payment_method?></td>
				</tr>
				<? if($booking->payment_method == 'Credit Cards') { ?>
				<tr>
					<td class="rezgo-td-label" valign="top">Card Number</td><td class="rezgo-td-data"><?=$booking->card_number?></td>
				</tr>
				<? } ?>
				<? if($site->exists($booking->payment_method_add->label)) { ?>
				<tr>
					<td class="rezgo-td-label" valign="top"><?=$booking->payment_method_add->label?></td><td class="rezgo-td-data"><?=$booking->payment_method_add->value?></td>
				</tr>
				<? } ?>
			<? } ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Payment Status</td>
				<td class="rezgo-td-data"><?=(($booking->status == 1) ? 'CONFIRMED' : '')?><?=(($booking->status == 2) ? 'PENDING' : '')?><?=(($booking->status == 3) ? 'CANCELLED' : '')?></td>
			</tr>
			<? if($site->exists($booking->trigger_code)) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top"><span>Promotional Code</span></td>
				<td class="rezgo-td-data"><?=$booking->trigger_code?></td>
			</tr>
			<? } ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Charges</td>
				<td class="rezgo-td-data">
					<table cellspacing="1" cellpadding="0" class="table table-bordered">
						<tr>
							<td align="right" style="padding:6px; font-weight:bold; width:35%;">Type</td>
							<td align="right" style="padding:6px; font-weight:bold; width:15%;">Qty</td>
							<td align="right" style="padding:6px; font-weight:bold; width:25%;">Cost</td>
							<td align="right" style="padding:6px; font-weight:bold; width:25%;">Total</td>
						</tr>
						
						<? foreach( $site->getBookingPrices() as $price ): ?>
						
						<tr>
							<td align="right" style="padding:6px;"><?=$price->label?></td>
							<td align="right" style="padding:6px;"><?=$price->number?></td>
							<td align="right" style="padding:6px;">
							<? if($site->exists($price->base)) { ?>
								<del><?=$site->formatCurrency($price->base)?></del>
							<? } ?>
							&nbsp;<?=$site->formatCurrency($price->price)?></td>
							<td align="right" style="padding:6px;"><?=$site->formatCurrency($price->total)?></td>
							
						</tr>
							
						<? endforeach; ?>
						
						<tr>
							<td colspan="3" style="padding:6px;" align="right"><strong>Sub-total</strong></td>
							<td align="right" style="padding:6px;"><?=$site->formatCurrency($booking->sub_total)?></td>
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
						
							<tr>
								<td colspan="3" style="padding:6px;" align="right"><?=$line->label?><?=$label_add?></td>
								<td align="right" style="padding:6px;"><?=$site->formatCurrency($line->amount)?></td>
							</tr>
						
						<? } ?>
						
						<? foreach( $site->getBookingFees() as $fee ): ?>
							<? if( $site->exists($fee->total_amount) ): ?>
								<tr>
									<td colspan="3" style="padding:6px;" align="right"><strong><?=$fee->label?></strong></td>
									<td align="right" style="padding:6px;"><?=$site->formatCurrency($fee->total_amount)?></td>
								</tr>
							<? endif; ?>
						<? endforeach; ?>
						
						<tr>
							<td colspan="3" style="padding:6px;" align="right"><strong>Total</strong></td>
							<td align="right" style="padding:6px;"><strong><?=$site->formatCurrency($booking->overall_total)?></strong></td>
						</tr>
						
						<? if($site->exists($booking->deposit)) { ?>
							<tr>
								<td colspan="3" style="padding:6px;" align="right"><strong>Deposit</strong></td>
								<td align="right" style="padding:6px;"><strong><?=$site->formatCurrency($booking->deposit)?></strong></td>
							</tr>
						<? } ?>
						
						<? if($site->exists($booking->overall_paid)) { ?>
							<tr>
								<td colspan="3" style="padding:6px;" align="right"><strong>Total Paid</strong></td>
								<td align="right" style="padding:6px;"><strong><?=$site->formatCurrency($booking->overall_paid)?></strong></td>
							</tr>
							<tr>
								<td colspan="3" style="padding:6px;" align="right"><strong>Total&nbsp;Owing</strong></td>
								<td align="right" style="padding:6px;"><strong><?=$site->formatCurrency(((float)$booking->overall_total - (float)$booking->overall_paid))?></strong></td>
							</tr>
						<? } ?>
						
					</table>
				
				</td>
			</tr>
		</table>		

	<? if(count($site->getBookingForms()) > 0 OR count($site->getBookingPassengers()) > 0) { ?>
		<h2>Guest Information</h2>
		
		<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
			<? foreach( $site->getBookingForms() as $form ): ?>
				<? if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
					<? if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
				<? } ?>
				<tr>
					<td class="rezgo-td-label" valign="top"><?=$form->question?>:</td>
					<td class="rezgo-td-data"><?=$form->answer?></td>
				</tr>
			<? endforeach; ?>		
			
			<? foreach( $site->getBookingPassengers() as $passenger ): ?>
				<tr>
					<td class="rezgo-td-label" valign="top"><?=$passenger->label?> <?=$passenger->num?>:</td>
					<td class="rezgo-td-data"><?=$passenger->first_name?> <?=$passenger->last_name?></td>
				</tr>
				<? if((string) $passenger->phone_number != '') { ?>			
				<tr>
					<td class="rezgo-td-label" valign="top">Phone Number:</td>
					<td class="rezgo-td-data"><?=$passenger->phone_number?></td>
				</tr>
				<? } 
				if((string) $passenger->email_address != '') {
				?>
				<tr>
					<td class="rezgo-td-label" valign="top">Email:</td>
					<td class="rezgo-td-data"><?=$passenger->email_address?></td>
				</tr>
				<? } ?>
				<? foreach( $passenger->forms->form as $form ): ?>
					<? if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
						<? if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
 					<? } ?>
					<tr>
						<td class="rezgo-td-label" valign="top"><?=$form->question?>:</td>
						<td class="rezgo-td-data"><?=$form->answer?></td>
					</tr>
				<? endforeach; ?>
				<tr>
					<td class="rezgo-td-label" valign="top">&nbsp;</td>
					<td class="rezgo-td-data">&nbsp;</td>
				</tr>				
			<? endforeach; ?>
		</table>		
		

	<? } ?>
	
		<h2>Customer Service</h2>
		
		<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
			<tr>
				<td class="rezgo-td-label" valign="top">Cancellation Policy</td>
				<td class="rezgo-td-data">
				<? if($site->exists($booking->rezgo_gateway)) { ?>
					
					Canceling a booking with Rezgo can result in cancellation fees being
					applied by Rezgo, as outlined below. Additional fees may be levied by
					the individual supplier/operator (see your Rezgo 
					<? echo ((string) $booking->ticket_type == 'ticket') ? 'Ticket' : 'Voucher' ?> for specific
					details). When canceling any booking you will be notified via email,
					facsimile or telephone of the total cancellation fees.<br />
					<br />
					1. Event, Attraction, Theater, Show or Coupon Ticket<br />
					These are non-refundable in all circumstances.<br />
					<br />
					2. Gift Certificate<br />
					These are non-refundable in all circumstances.<br />
					<br />
					3. Tour or Package Commencing During a Special Event Period<br />
					These are non-refundable in all circumstances. This includes,
					but is not limited to, Trade Fairs, Public or National Holidays,
					School Holidays, New Year's, Thanksgiving, Christmas, Easter, Ramadan.<br />
					<br />
					4. Other Tour Products & Services<br />
					If you cancel at least 7 calendar days in advance of the
					scheduled departure or commencement time, there is no cancellation
					fee.<br />
					If you cancel between 3 and 6 calendar days in advance of the
					scheduled departure or commencement time, you will be charged a 50%
					cancellation fee.<br />
					If you cancel within 2 calendar days of the scheduled departure
					or commencement time, you will be charged a 100% cancellation fee.
					<br />
				<? } else { ?>
					<? if($site->exists($item->details->cancellation)) { ?>
						<?=$site->cleanAttr($item->details->cancellation)?>
						<br />
					<? } ?>
				<? } ?>
				
				View terms and conditions: <strong>http://<?=$site->getDomain()?>.rezgo.com/terms</strong>
				</td>
			</tr>
			
			<? if($site->exists($booking->rid)) { ?>
			<tr>
				<td class="rezgo-td-label" valign="top">Customer Service</td>
				<td class="rezgo-td-data">
				<? if($site->exists($booking->rezgo_gateway)) { ?>
					
					Rezgo.com<br />
					Attn: Partner Bookings<br />
					333 Brooksbank Avenue<br />
					Suite 718<br />
					North Vancouver, BC<br />
					Canada V7J 3V8<br />
					(604) 983-0083<br />
					bookings@rezgo.com
					
				<? } else { ?>
								
					<? $company = $site->getCompanyDetails('p'.$booking->rid); ?>
					<?=$company->company_name?><br />
					<?=$company->address_1?> <?=$company->address_2?><br />
					<?=$company->city?>, <? if($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?><?=$site->countryName($company->country)?><br />
					<?=$company->postal_code?><br />
					<?=$company->phone?><br />
					<?=$company->email?>
					<? if($site->exists($company->tax_id)) { ?>
					<br />
					<br />
					<?=$company->tax_id?>
					<? } ?>

				<? } ?>

				</td>
			</tr>
			
			<? } ?>

			<tr>
				<td class="rezgo-td-label" valign="top">Service Provided By</td>
				<td class="rezgo-td-data">
				<? $company = $site->getCompanyDetails($booking->cid); ?>
				<?=$company->company_name?><br />
				<?=$company->address_1?> <?=$company->address_2?><br />
				<?=$company->city?>, <? if($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?><?=$site->countryName($company->country)?><br />
				<?=$company->postal_code?><br />
				<?=$company->phone?><br />
				<?=$company->email?>
				<? if($site->exists($company->tax_id)) { ?>
				<br />
				Tax ID: <?=$company->tax_id?>
				<? } ?>
				</td>
			</tr>
		</table>		
		 
	
	<? endforeach; ?>


</body>
</html>