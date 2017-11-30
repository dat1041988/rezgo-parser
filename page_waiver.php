<?php 
	// This is the waiver page
	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = $_REQUEST['sec'] ? new RezgoSite(secure) : new RezgoSite();

	// Page title
	$site->setPageTitle($_REQUEST['title'] ? $_REQUEST['title'] : 'Waiver');
?>

<?=$site->getTemplate('frame_header')?>

<?=$site->getTemplate('waiver')?>

<?=$site->getTemplate('frame_footer')?>