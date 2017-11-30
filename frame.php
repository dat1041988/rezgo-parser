<?php 
	// any new page must start with the page_header, it will include the correct files
	// so that the rezgo parser classes and functions will be available to your templates

	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = new RezgoSite($_REQUEST['sec'], 1);

	// GET COMPANY DETAILS
	$company = $site->getCompanyDetails();

	// remove the 'mode=page_type' from the query string we want to pass on
	$_SERVER['QUERY_STRING'] = preg_replace("/([&|?])?mode=([a-zA-Z_]+)/", "", $_SERVER['QUERY_STRING']);

	// set a default page title
	$site->setPageTitle((($_REQUEST['title']) ? $_REQUEST['title'] : ucwords(str_replace("page_", "", $_REQUEST['mode']))));
	$site->setMetaTags('<link rel="canonical" href="'.(string) $company->primary_domain.'" />');

	if($_REQUEST['mode'] == 'page_details') {
		/*
			this query searches for an item based on a com id (limit 1 since we only want one response)
			then adds a $f (filter) option by uid in case there is an option id, and adds a date in case there is a date set	
		*/

		$item = $site->getTours('t=com&q='.$_REQUEST['com'].'&f[uid]='.$_REQUEST['option'].'&d='.$_REQUEST['date'].'&limit=1', 0);

		// if the item does not exist, we want to generate an error message and change the page accordingly
		if(!$item) {
			$item = new stdClass();
			$item->unavailable = 1;
			$item->name = 'Item Not Available'; 
		}

		if ($item->seo->seo_title != '') {
			$page_title = $item->seo->seo_title;
		} 
		else {
			$page_title = $item->item;
		}

		if ($item->seo->introduction != '') {
			$page_description = $item->seo->introduction;
		} 
		else {
			$page_description = strip_tags($item->details->overview);
		}

		$site->setPageTitle($page_title);

		$site->setMetaTags('
			<meta name="description" content="' . $page_description . '" /> 
			<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']  . '" /> 
			<meta property="og:title" content="' . $page_title . '" /> 
			<meta property="og:description" content="' . $page_description . '" /> 
			<meta property="og:image" content="' . $item->media->image[0]->path . '" /> 
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<link rel="canonical" href="http://'.(string) $company->primary_domain.$_SERVER['REQUEST_URI'].'" />
		');
	} 
	elseif($_REQUEST['mode'] == 'page_content') {
		$title = $site->getPageName($page);

		$site->setPageTitle($title);

	}
	elseif($_REQUEST['mode'] == 'index') {
		
		// expand to include keywords and dates
		$site->setPageTitle((($_REQUEST['tags']) ? ucwords($_REQUEST['tags']) : 'Home'));
		
	}

	$_SERVER['QUERY_STRING'] .= '&title=' . $site->pageTitle;
?>

<?=$site->getTemplate('header')?>
  
<script type="text/javascript">
// for iFrameResize native version
// MDN PolyFil for IE8 
if (!Array.prototype.forEach){
	Array.prototype.forEach = function(fun /*, thisArg */){
        "use strict";
        if (this === void 0 || this === null || typeof fun !== "function") throw new TypeError();

        var
            t = Object(this),
            len = t.length >>> 0,
            thisArg = arguments.length >= 2 ? arguments[1] : void 0;

        for (var i = 0; i < len; i++)
            if (i in t)
                fun.call(thisArg, t[i], i, t);
    };
}	
</script>

<div id="rezgo_content_container" style="width:100%;">
	<iframe id="rezgo_content_frame" name="rezgo_content_frame" src="<?=$site->base?>/<?=$_REQUEST['mode']?>?<?=$_SERVER['QUERY_STRING']?>" style="width:100%; height:900px; padding:0px; margin:0px;" frameBorder="0" scrolling="no"></iframe>
</div>

<!-- Modal -->
<div id="rezgo-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>

				<h4 id="rezgo-modal-title" class="modal-title"></h4>
			</div>

			<iframe id="rezgo-modal-iframe" frameBorder="0" scrolling="no" style="width:100%; padding:0px; margin:0px;"></iframe>

			<div id="rezgo-modal-loader" style="display:none">
				<div class="modal-loader"></div>
			</div>
		</div>
	</div>
</div>
<!--TESTING-->
<link href="<?=$site->path?>/css/bootstrap-modal.css" rel="stylesheet" />
<link href="<?=$site->path?>/css/rezgo-modal.css" rel="stylesheet" />
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$site->base?>/js/iframeResizer.min.js"></script>

<script type="text/javascript">
	iFrameResize ({
		enablePublicMethods: true,
		scrolling: true
	});
</script>

<?=$site->getTemplate('footer')?>
