<?php
require(dirname(__FILE__) . '/App/App.php');
try{
	// header($_SERVER['SERVER_PROTOCOL'] . ' 200 Success', true, 200);
	// header('Content-type:application/json;charset=utf-8');
	// die(json_encode($_REQUEST));
	// throw new Exception('asdf');
	$today			= date('Y-m-d');
	$dateStart		= !empty($_REQUEST['dateRange']['start'])	? trim($_REQUEST['dateRange']['start'])	: date('Y-m-d');
	if(strtotime($dateStart) < strtotime($today))
		$dateStart = $today;
	$dateRange		= [
		'start'	=> $dateStart,
		'end'	=> date('Y-m-t', strtotime($dateStart))
	];
	$offset	= [
		'dist'	=> [
			'x'	=> !empty($_REQUEST['offset']['dist']['x'])		? trim($_REQUEST['offset']['dist']['x'])	: 1.5,
			'y'	=> !empty($_REQUEST['offset']['dist']['y'])		? trim($_REQUEST['offset']['dist']['y'])	: 4,
		],
		'cells'	=> [
			'x'	=> !empty($_REQUEST['offset']['cells']['x'])	? trim($_REQUEST['offset']['cells']['x'])	: 5,
			'y'	=> !empty($_REQUEST['offset']['cells']['y'])	? trim($_REQUEST['offset']['cells']['y'])	: 5,
		]
	];
	$start = [
		// 'lat'	=> 45,
		// 'lng'	=> -120,
		'lat'	=> !empty($_REQUEST['start']['lat'])	? trim($_REQUEST['start']['lat'])	: 50,
		'lng'	=> !empty($_REQUEST['start']['lng'])	? trim($_REQUEST['start']['lng'])	: -130,
	];
	$calendar = new Calendar(
		$dateRange,
		$offset,
		$start
	);
	// $dateGrid = $calendar->getDateGrid();
	header($_SERVER['SERVER_PROTOCOL'] . ' 200 Success', true, 200);
	header('Content-type:application/json;charset=utf-8');
	die(json_encode([
		'data'		=> [
			'dateGrid'	=> $calendar->getDateGrid(),
			'request'	=> [
				'original'	=> $_REQUEST,
				'parsed'	=> [
					'dateRange'	=> $dateRange,
					'offset'	=> $offset,
					'start'		=> $start
				]
			]
		],
		// 'errors'	=> []
	]));
}catch(Exception $e){
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	header('Content-type:application/json;charset=utf-8');
	die(json_encode([
		// 'data'		=> [
		// ],
		'errors'	=> [[
			// 'id'		=> null,
			// 'links'		=> [
				// 'about'	=> null,
			// ],
			'status'	=> 'Internal Server Error',
			'code'		=> 500,
			'title'		=> $e->getMessage(),
			// 'detail'	=> null,
			// 'source'	=> [
				// 'pointer'	=> null,
				// 'parameter'	=> null,
			// ],
			// 'meta'	=> [
				// 'copyright'	=> 'Copyright ' . date('Y') . ' Tyler Green',
				// 'authors'	=> 'Tyler Green'
			// ],
		]]
	]));
}