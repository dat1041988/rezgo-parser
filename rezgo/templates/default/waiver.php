<link rel="stylesheet" href="<?=$this->path?>/css/signature-pad.css" />

<style media="print">
	#rezgo-waiver-wrp.rezgo-modal-wrp .tab-text .body {
		height: auto;
		overflow: visible;
	}
	#rezgo-waiver-wrp.rezgo-modal-wrp .tab-text .footer {
		display: none;
	}
	#scroll-down-info {
		display: none !important;
	}
</style>

<div id="rezgo-waiver-wrp" class="container-fluid rezgo-container <? if ($_SERVER['SCRIPT_NAME'] == '/modal.php') { ?>rezgo-modal-wrp<? } ?>">
	<div class="clearifx">
		<? if($_SERVER['SCRIPT_NAME'] != '/modal.php') { ?>
			<div class="heading clearfix">
				<h1>Waiver</h1>

				<div id='rezgo-back' style='display: none'>
					<i class="fa fa-long-arrow-left" aria-hidden="true"></i>
					<span>Go back</span>
				</div>
			</div>
			<hr>
		<? } ?>

		<div class="tab-text">
			<div class="body">
				<div class="row">
					<div class="col-md-12">
						<? if ($_SERVER['SCRIPT_NAME'] == '/modal.php') { ?>
							<?
							$str = '';
							$cart = $site->getCart(1);
							foreach($cart as $item) {
								$site->readItem($item);

								$str .= (string) $item->uid.',';
							}
							?>

							<div id='scroll-down-info' class='alert alert-warning fade in alert-dismissable' style='display:none'>
								<span>Scroll down to enable the sign waiver button</span>
							</div>

							<? 
							echo $site->getWaiverContent(rtrim($str,','));

							} else {
							$url = 'https://api.john.rezgo.com/xml?transcode='.REZGO_CID.'&i=waiver&q='.$_REQUEST['order_num'];

							$xml = new SimpleXMLElement(file_get_contents($url));

							echo $xml->waiver;
						} ?>

						<div id='signature-area' style='display:none;'>
							<hr>

							<div class="row">
								<div class="col-xs-12">
									<small>Signature:</small>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12">
									<img id='signature-img' alt='signature' />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<? if ($_SERVER['SCRIPT_NAME'] != '/modal.php') { ?>
				<hr>
			<? } ?>

			<div class="footer">
				<div class="row">
					<div id="rezgo-sign-nav">
						<div class="col-xs-6">
							<button id="sign" class="btn rezgo-btn-default btn-block" <? if ($_SERVER['SCRIPT_NAME'] == '/modal.php') { ?>disabled<? } ?>>
								<i class="fa fa-pencil bigger-110"></i>
								<span id="rezgo-sign-nav-txt"> Sign Waiver</span>
							</button>
						</div>

						<div class="col-xs-6">
							<button id="print" class="btn rezgo-btn-book btn-block">
								<i class="fa fa-print bigger-110"></i>
								<span> Print Waiver</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-sign" style="display:none;">
			<div id="signature-pad">
				<div class="body">
					<p>Please sign in the space below</p>
					<canvas></canvas>
				</div>
				<div class="footer">
					<div class="row">
						<div class="col-xs-6">
							<button id="clear" class="btn rezgo-btn-default btn-block" data-action="clear" type="button">
								<i class="fa fa-times bigger-110"></i>
								<span> Clear</span>
							</button>
						</div>
						<div class="col-xs-6">
							<button id="save" class="btn rezgo-btn-book btn-block" data-action="save" type="button">
								<i class="fa fa-check bigger-110"></i>
								<span> Save</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="/rezgo/templates/default/js/signature_pad.min.js"></script>
<script src="/rezgo/templates/default/js/signature_pad_remove_blank.js"></script>
<script>
	var 
	signature = parent.rezgo_content_frame.document.getElementById('rezgo-waiver-input').value,
	receiver = window.top.document.getElementById('rezgo_content_frame').contentWindow,
	waiverModal = document.getElementById('rezgo-waiver-wrp'),
	signButton = document.getElementById('sign'),
	signBtnTxt = document.getElementById('rezgo-sign-nav-txt'),
	printButton = document.getElementById('print'),
	saveButton = document.getElementById('save'),
	signaturePad = document.getElementById('signature-pad'),
	clearButton = signaturePad.querySelector('[data-action=clear]'),
	undoButton = signaturePad.querySelector('[data-action=undo]'),
	canvas = signaturePad.querySelector('canvas'),
	waiverTxt = waiverModal.getElementsByClassName('tab-text')[0],
	waiverTxtBody = waiverTxt.getElementsByClassName('body')[0],
	waiverSignArea = document.getElementById('signature-area'),
	waiverSignImg = document.getElementById('signature-img'),
	scrollDownInfo = document.getElementById('scroll-down-info'),
	signaturePad = new SignaturePad(canvas);

	<? if ($_SERVER['SCRIPT_NAME'] != '/modal.php') { ?>
		var
		backButton = document.getElementById('rezgo-back'),
		waiverNav = document.getElementById('rezgo-sign-nav'),
		waiverSigned = document.getElementById('rezgo-waiver-signed'),
		waiverSignature = document.getElementById('rezgo-waiver-signature');
	<? } ?>

	function resizeCanvas() {
		var ratio =  Math.max(window.devicePixelRatio || 1, 1);
		canvas.width = canvas.offsetWidth * ratio;
		canvas.height = canvas.offsetHeight * ratio;
		canvas.getContext("2d").scale(ratio, ratio);
		signaturePad.clear();
	}
	function printWaiver(e) {
		setTimeout(function() { 
			window.focus(); 
			window.print(); 
		}, 200);
	}
	function showSignaturePad(e) {
		$(".tab-text").hide();
		$(".tab-sign").show();
		<? if ($_SERVER['SCRIPT_NAME'] != '/modal.php') { ?>
			$("#rezgo-back").show();
		<? } ?>
		resizeCanvas();
	}
	function clearSignature(e) {
		signaturePad.clear();
	}
	function check_scroll(e) {
		var elem = e.target;

		console.log(elem.scrollTop);
		console.log(elem.scrollHeight - elem.offsetHeight);
		
		if(elem.scrollTop >= (elem.scrollHeight - elem.offsetHeight)) {
			signButton.disabled = false;
		}
	}
	function checkOverflow(el) {
		var curOverflow = el.style.overflow;

		if(!curOverflow || curOverflow === "visible") el.style.overflow = "hidden";

		var isOverflowing = el.clientHeight < el.scrollHeight;

		el.style.overflow = curOverflow;

		return isOverflowing;
	}
	function saveSignature(e) {
		if (signaturePad.isEmpty()) {
			alert("Please provide a signature first.");
		} else {
			e.preventDefault();

			canvas.style.visibility = 'hidden';

			signaturePad.removeBlanks();

			addSignature(signaturePad.toDataURL());

			<? if ($_SERVER['SCRIPT_NAME'] == '/modal.php') { ?>
				var msg = {
					type:'modal',
					mode:'get_waiver',
					sig: signaturePad.toDataURL()
				};

				receiver.postMessage(msg, '*');
			<? } else { ?>
				// console.log('Waiver signed.. Use ajax to update DB and redirect..');

				back();
			<? } ?>
		}
	}
	function addSignature(req) {
		waiverSignArea.style.display = 'block';

		waiverSignImg.src = req;

		signBtnTxt.innerHTML = 're-sign waiver';
	}
	function back() {
		backButton.style.display = "none";
		$(".tab-text").show();
		$(".tab-sign").hide();
	}

	saveButton.addEventListener('click', saveSignature);
	signButton.addEventListener('click', showSignaturePad);
	printButton.addEventListener('click', printWaiver);
	clearButton.addEventListener('click', clearSignature);

	window.onresize = resizeCanvas;

	<? if ($_SERVER['SCRIPT_NAME'] == '/modal.php') { ?>
		waiverTxtBody.addEventListener('scroll', check_scroll);

		if (!checkOverflow(waiverTxtBody)) {
			signButton.disabled = false;
		} else {
			scrollDownInfo.style.display = 'block';
		}

		if(signature !== '') {
			addSignature(signature);
		}
	<? } else { ?>
		backButton.addEventListener('click', back);
	<? } ?>
</script>