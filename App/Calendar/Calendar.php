<?php
class Calendar { 
	
	private $moon			= null;	// Moon class
	private $dateRange		= null;
	private $coordinates	= [
		'start'	=> [
			'lat'	=> 45,
			'lng'	=> -120,
		],
		'offset'	=> [
			'distX'	=> .35,
			'distY'	=> .5,
			'x'		=> 2,
			'y'		=> 2,
		]
	];
	
	public function __construct(int $days = 30){
		$this->moon			= new Moon();
		$this->dateRange	= $days;
	}
	
	public function getDateList(){
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
	}
	
	public function getDateGrid(){
		$dateStart		= new DateTime();
		$dateEnd		= new DateTime('+' . $this->dateRange . ' days');
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
		$its	= $this->coordinates['offset']['x'] * $this->coordinates['offset']['y'];
		$row	= 1;
		$col	= 1;
		$rowIts	= 0;
		$grid	= [];
		for($i = $its; $i >= 1; $i--){
			if($rowIts === $this->coordinates['offset']['x']){
				$row++;
				$rowIts	= 0;
				$col	= 1;
			}
// var_dump('i:' . $i . ' row:' . $row . ' col:' . $col);
			$poly = $this->buildPolygon($row, $col);
			$lat = $poly['c']['lat'];
			$lng = $poly['c']['lng'];
			$grid[] = [
				'sun'	=> date_sun_info(
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
			$col++;
			$rowIts++;
// var_dump($grid);
		}
		return $grid;
	}
	
	private function buildPolygon($row, $col){
		$tLat		= $this->coordinates['start']['lat'];
		$tLng		= $this->coordinates['start']['lng'];
		$offsetX	= $this->coordinates['offset']['distX'] * $row;
		$offsetY	= $this->coordinates['offset']['distY'] * $col;
		$poly		= [
			't'	=> [
				'lat'	=> $tLat,
				'lng'	=> $tLng,
			],
			'r'	=> [
				'lat'	=> $tLat - $offsetX,
				'lng'	=> $tLng,
			],
			'b'	=> [
				'lat'	=> $tLat - $offsetX,
				'lng'	=> $tLng + $offsetY,
			],
			'l'	=> [
				'lat'	=> $tLat,
				'lng'	=> $tLng + $offsetY,
			],
			'c'	=> [
				'lat'	=> ($tLat + $offsetX) / 2,
				'lng'	=> ($tLng + $offsetY) / 2,
			],
		];
// var_dump($poly);
		return $poly;
	}

}