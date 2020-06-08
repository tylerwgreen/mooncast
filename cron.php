<?php
require(dirname(__FILE__) . '/App/App.php');
$message = 'Success!';
try{
	$stateZone = weatherDataGetStateZone();
	$weatherData = weatherDataGet(
		$config->weatherAPI,
		$stateZone
	);
	weatherDataCache($stateZone, $weatherData);
}catch(Exception $e){
	$message = $e->getMessage();
}
require(TEMPLATE_HEADER);
?>
<body id="cron">
	<p><?= $message; ?></p>
<?php require(TEMPLATE_FOOTER); ?>
