<? $split = explode(",", $_REQUEST['trans_num']); ?>

<? foreach((array) $split as $v) { ?>
	<?
	$trans_num = $site->decode($v);
	if(!$trans_num) $site->sendTo("/");
	$booking = $site->getBookings($trans_num, 0);
	$checkin = (string) $booking->checkin;
	$availability_type = (string) $booking->availability_type;
	$checkin_state = $booking->checkin_state;
	$type = ((string) $booking->ticket_type != '' ? $booking->ticket_type : 'voucher'); 
	?>

	<? if($checkin && $availability_type != 'product') { ?>
		<? $ticket_content = $site->getTicketContent($trans_num, 0); ?>

		<? foreach($ticket_content->tickets as $ticket_list) { ?>
			<? foreach ($ticket_list as $ticket) { ?>
				<?=$ticket?><br />
				<div class="h6 pull-right">
					<span class="rezgo-ticket-logo">Rezgo</span>
				</div>
				<div class="clearfix"></div>
				<hr class="rezgo-ticket-bottom" />
			<? } ?>
		<? } ?>
	<? } elseif(!$checkin && $availability_type != 'product') { ?>
		<? if ($booking->status == 3) { ?>
			<div class="col-xs-12 rezgo-print-hide"><br />Booking <strong><?=$trans_num?></strong> has been cancelled, ticket is not available.<br /><br /></div>
		<? } else { ?>
			<div class="col-xs-12 rezgo-print-hide"><br /><?=ucwords($type)?> for Booking <strong><?=$trans_num?></strong> is not available until the booking has been confirmed.<br /><br /></div>
		<? } ?>
    
		<div class="h6 pull-right"><span class="rezgo-ticket-logo">Rezgo</span></div>
	
	<? } else { ?>
  
    <div class="col-xs-12 rezgo-print-hide"><br /><?=ucwords($type)?> is not available for product purchase <strong><?=$trans_num?></strong>.<br /><br /></div>
		<div class="h6 pull-right"><span class="rezgo-ticket-logo">Rezgo</span></div>
    
	<? } ?>

	<div class="clearfix"></div>

	<? if(count($split) > 1) { ?>
		<div class="col-xs-12" style="border-top:1px solid #CCC; page-break-after: always;"></div>
	<? } ?>
<? } ?>
