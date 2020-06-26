<?php
require(dirname(__FILE__) . '/App/App.php');
require(TEMPLATE_HEADER);
?>
<body id="map">
	<ul id="date-display">
		<li id="month-selector">
			<label for="month">Month:</label>
			<select name="month" id="month">
				<?php $selectedSet = false; foreach(getMonths() as $monthNum => $monthName): ?>
					<option value="<?= $monthNum; ?>"<?= $selectedSet ? '' : ' selected'; ?>><?= $monthName; ?></option>
				<?php $selectedSet = true; endforeach; ?>
			</select>
		</li>
		<li>Date:<span id="date-display-date"></span></li>
		<li>Sunrise:<span id="date-display-sunrise"></span></li>
		<li>Sunset:<span id="date-display-sunset"></span></li>
		<li>Moonrise:<span id="date-display-moonrise"></span></li>
		<li>Moonset:<span id="date-display-moonset"></span></li>
	</ul>
	<div id="map-container"></div>
	<div id="date-slider">
		<div id="date-slider-slider">
			<div id="date-slider-custom-handle" class="ui-slider-handle"></div>
		</div>
	</div>
	<div id="loader-wrap">
		<div id="loader">
			<img src="<?= URL_IMG; ?>loader.gif" alt="Loading..."/>
		</div>
		<div id="loader-bg"></div>
	</div>
	<script>
		var debug = <?= DEBUG ? 'true' : 'false'; ?>;
		var scriptData = <?= json_encode(['urls' => ['base' => baseUrl(), 'data' => baseUrl() . 'data.php']]); ?>;
	</script>
	<?php
	$footerScripts = 
	'<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $config->google->mapsApiKey . '&callback=initMap"></script>';
	?>
<?php require(TEMPLATE_FOOTER); ?>