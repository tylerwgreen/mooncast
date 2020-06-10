<?php
require(dirname(__FILE__) . '/App/App.php');
try{
	$calendar = new Calendar();
	// $weather = weatherDataGetCached();
}catch(Exception $e){
	die($e->getMessage());
}
require(TEMPLATE_HEADER);
?>
<body id="index">
	<?= $calendar->show(); ?>
<?php require(TEMPLATE_FOOTER); ?>