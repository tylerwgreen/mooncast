<?php
class Calendar {
	
	private $moon			= null;	// Moon class
	private $dateRange		= [
		'start'	=> null,
		'end'	=> null,
	];
	private $coordinates	= [
		'start'	=> [
			'lat'	=> null,
			'lng'	=> null
		],
		'offset'	=> [
			'dist'	=> [
				'x'	=> null,
				'y'	=> null
			],
			'cells'	=> [
				'x'	=> null,
				'y'	=> null
			]
		]
	];
	
	public function __construct(array $dateRange, array $offset, array $start){
		$this->moon						= new Moon();
		$this->dateRange				= $dateRange;
		$this->coordinates['offset']	= $offset;
		$this->coordinates['start']		= $start;
// print_r($this);
	}
	
	/* public function getDateList(){
		$dateStart	= new DateTime();
		$dateEnd	= new DateTime('+' . $this->dateRange . ' days');
		$period		= new DatePeriod(
			$dateStart,
			new DateInterval('P1D'),
			$dateEnd
		);
		$dates		= [];
		foreach($period as $k => $date){
			$dates[] = [
				'date'		=> $date->format('Y-m-d'),
				'timestamp'	=> $date->getTimestamp(),
				'readable'	=> $date->format('D, M jS, Y'),
				'day'		=> $date->format('j'),
				'dayOfWeek'	=> $date->format('D'),
				'month'		=> $date->format('M'),
				'year'		=> $date->format('Y'),
			];
		}
		return $dates;
	} */
	
	public function getDateGrid(){
		$dateStart		= new DateTime($this->dateRange['start']);
		$dateEnd		= new DateTime($this->dateRange['end']);
		$period			= new DatePeriod(
			$dateStart,
			new DateInterval('P1D'),
			$dateEnd
		);
		$dates			= [];
		foreach($period as $k => $date){
			$dates[] = [
				'date'		=> $date->format('Y-m-d'),
				'timestamp'	=> $date->getTimestamp(),
				'readable'	=> $date->format('D, M jS, Y'),
				'day'		=> $date->format('j'),
				'dayOfWeek'	=> $date->format('D'),
				'month'		=> $date->format('M'),
				'year'		=> $date->format('Y'),
				'grid'		=> $this->buildGrid($date),
			];
		}
		return $dates;
	}
	
	private function buildGrid($date){
// print_r($date->format('Y-m-d g:i:s a'));
		$its	= $this->coordinates['offset']['cells']['x'] * $this->coordinates['offset']['cells']['y'];
		$row	= 1;
		$col	= 1;
		$rowIts	= 0;
		$grid	= [];
		for($i = $its; $i >= 1; $i--){
			if($rowIts == $this->coordinates['offset']['cells']['x']){
				$row++;
				$rowIts	= 0;
				$col	= 1;
			}
			$poly = $this->buildPolygon($row, $col);
			$lat = $poly['c']['lat'];
			$lng = $poly['c']['lng'];
// print_r($poly['c']);
			$cell = [
				'sun'	=> date_sun_info(
// strtotime($date->format('Y-m-d')),
					$date->getTimestamp(),
					$lat,
					$lng
				),
				'moon'	=> $this->moon->calculateMoonTimes(
					$date->format('m'),
					$date->format('d'),
					$date->format('Y'),
					$lat,
					$lng
				),
				'poly'	=> $poly,
			];
// $cell['sun']['readable'] = $this->makeReadable($cell['sun']);
			$cell['ratings']['sunset']['moonrise']		= $this->calculateRating($cell['sun']['sunset'], $cell['moon']->moonrise);
			$cell['ratings']['sunset']['moonset']		= $this->calculateRating($cell['sun']['sunset'], $cell['moon']->moonset);
			$cell['ratings']['sunset']['combined']		= ($cell['ratings']['sunset']['moonrise'] + $cell['ratings']['sunset']['moonset']) / 2;
			$cell['ratings']['sunrise']['moonrise']		= $this->calculateRating($cell['sun']['sunrise'], $cell['moon']->moonrise);
			$cell['ratings']['sunrise']['moonset']		= $this->calculateRating($cell['sun']['sunrise'], $cell['moon']->moonset);
			$cell['ratings']['sunrise']['combined']		= ($cell['ratings']['sunrise']['moonrise'] + $cell['ratings']['sunrise']['moonset']) / 2;
			$cell['ratings']['combined']				= ($cell['ratings']['sunset']['combined'] + $cell['ratings']['sunrise']['combined']) / 2;
			
			$max = $cell['ratings']['sunset']['moonrise'];
			if($cell['ratings']['sunset']['moonset'] > $max)
				$max = $cell['ratings']['sunset']['moonset'];
			if($cell['ratings']['sunrise']['moonrise'] > $max)
				$max = $cell['ratings']['sunrise']['moonset'];
			if($cell['ratings']['sunrise']['moonrise'] > $max)
				$max = $cell['ratings']['sunrise']['moonrise'];
			if($cell['ratings']['sunrise']['moonset'] > $max)
				$max = $cell['ratings']['sunrise']['moonset'];
			$cell['ratings']['max']						= $max;
			
// print_r($cell['sun']);
			$grid[] = $cell;
			$col++;
			$rowIts++;
		}
		return $grid;
	}
	
	/* private function makeReadable($data){
		$out = [''];
		foreach($data as $k => $v){
			$out[$k] = date('Y-m-d g:i:s a', $v);
		}
		return $out;
	} */
	
	private function calculateRating($sun, $moon){
		$diff = abs($sun - $moon);
$hour = 60 * 60 * 3; // 3 hours
		// $hour = 60 * 60;
		if($diff > $hour)
			return 0;
		if($diff == 0)
			return 100;
		return $hour / $diff;
	}

	private function buildPolygon($row, $col){
		$offsetX	= $this->coordinates['offset']['dist']['x'];
		$offsetY	= $this->coordinates['offset']['dist']['y'];
		$tLat		= $this->coordinates['start']['lat'] - $offsetX * ($row - 1);
		$tLng		= $this->coordinates['start']['lng'] + $offsetY * ($col - 1);
		$poly		= [
			't'	=> [
				'lat'	=> $tLat,
				'lng'	=> $tLng,
			],
			'r'	=> [
				'lat'	=> $tLat,
				'lng'	=> $tLng + $offsetY,
			],
			'b'	=> [
				'lat'	=> $tLat - $offsetX,
				'lng'	=> $tLng + $offsetY,
			],
			'l'	=> [
				'lat'	=> $tLat - $offsetX,
				'lng'	=> $tLng,
			]
		];
		$poly['c'] = [
			'lat'	=> ($poly['t']['lat'] + $poly['b']['lat']) / 2,
			'lng'	=> ($poly['t']['lng'] + $poly['b']['lng']) / 2,
		];
		return $poly;
	}

}