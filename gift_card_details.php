<?php 
	// This is the gift card page
	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = new RezgoSite();
?>

<?=$site->getTemplate('frame_header')?>

<?=$site->getTemplate('gift_card_details')?>

<?=$site->getTemplate('frame_footer')?>