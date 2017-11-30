<? if($_REQUEST['mode']=='waiver') include('page_waiver.php'); ?>

<script>
	function removeLoader() {
		var loader = window.top.document.getElementById('rezgo-modal-loader');

		loader.style.display = 'none';
	}
	window.onload = function(){
		removeLoader();
	}
</script>
