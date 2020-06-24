<?php
require(dirname(__FILE__) . '/App/App.php');
try{
	$calendar = new Calendar(7);
	$dateList = $calendar->getDateList();
	$dateGrid = $calendar->getDateGrid();
// var_dump($dateList);
// var_dump($dateGrid);
	$mapData = [
		// 'dateList' => $dateList,
		'dateGrid' => $dateGrid,
	];
}catch(Exception $e){
	die($e->getMessage());
}
require(TEMPLATE_HEADER);
?>
<body id="map">
	<div id="date-display">
		<span id="date-display-readable"></span>
	</div>
	<div id="map-container"></div>
	<div id="date-slider">
		<div id="date-slider-slider">
			<div id="date-slider-custom-handle" class="ui-slider-handle"></div>
		</div>
	</div>
	<script>
		var debug = <?= DEBUG ? 'true' : 'false'; ?>;
		var mapData = <?= json_encode($mapData); ?>;
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= $config->google->mapsApiKey; ?>&callback=initMap"></script>
<?php require(TEMPLATE_FOOTER); ?>