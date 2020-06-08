<?php
require(dirname(__FILE__) . '/App/App.php');
try{
	$weather = weatherDataGetCached();
}catch(Exception $e){
	die($e->getMessage());
}
require(TEMPLATE_HEADER);
?>
<body id="map">
	<div id="data-container">
		<div id="weather-selector">
			<ul id="weather-type">
				<li id="weather-type-fog" data-weather-type="fog">Fog</li>
				<li id="weather-type-thunder" data-weather-type="thunder">Thunder</li>
				<li id="weather-type-snow" data-weather-type="snow">Snow</li>
				<li id="weather-type-rain" data-weather-type="rain">Rain</li>
			</ul>
		</div>
		<div id="zone-data">
			<div id="zone-id" class="zone-data-item">Zone ID</div>
			<ul id="zone-overview">
				<li id="zone-overview-fog">Fog</li>
				<li id="zone-overview-thunder">Thunder</li>
				<li id="zone-overview-snow">Snow</li>
				<li id="zone-overview-rain">Rain</li>
			</ul>
			<div id="zone-updated" class="zone-data-item">Updated</div>
			<div id="zone-name" class="zone-data-item">Zone Name</div>
		</div>
		<div id="period-data">
			<ul id="period-names"></ul>
		</div>
	</div>
	<div id="map-container"></div>
	<div id="period-selector">
		<div id="period-selector-slider">
			<div id="period-selector-custom-handle" class="ui-slider-handle"></div>
		</div>
	</div>
	<div id="forecast-modal">
		<div id="forecast-modal-controls">
			<a id="forecast-modal-btn-close" href="#">Close</a>
		</div>
		<ul id="forecast-data">
			<li id="forecast-data-id-wrap">
				<a href="#" id="forecast-data-id" title="Hourly forecast" target="_blank"></a>
			</li>
			<li id="forecast-data-updated"></li>
			<li id="forecast-data-name-wrap">
				<a href="#" id="forecast-data-name" title="Map" target="_blank"></a>
			</li>
		</ul>
		<ul id="forecast-periods"></ul>
	</div>
	<script>
		var debug = <?= DEBUG ? 'true' : 'false'; ?>;
		var zonesData = <?= json_encode($weather); ?>;
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= $config->google->mapsApiKey; ?>&callback=initMap"></script>
	<script src="<?= URL_JS; ?>map.js"></script>
<?php require(TEMPLATE_FOOTER); ?>