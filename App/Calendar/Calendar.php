<?php
class Calendar { 

	private $weekStartsOnSunday = true;
	private $dayLabelsMon	= array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
	private $dayLabelsSun	= array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	private $dayLabels		= null;
	private $currentYear	= 0;
	private $currentMonth	= 0;
	private $currentDay		= 0;
	private $currentDate	= null;
	private $daysInMonth	= 0;
	private $naviHref		= null;
	
	public function __construct(){	 
		$this->naviHref = htmlentities($_SERVER['PHP_SELF']);
		$this->dayLabels = $this->weekStartsOnSunday ? $this->dayLabelsSun : $this->dayLabelsMon;
	}

	public function show(){
		$year	= !empty($_GET['y']) ? $_GET['y'] : date('Y');
		$month	= !empty($_GET['m']) ? $_GET['m'] : date('m');
		$this->currentYear	= $year;
		$this->currentMonth	= $month;
		$this->daysInMonth	= $this->_daysInMonth($month, $year);
		$weeksInMonth = $this->_weeksInMonth($month, $year);
		$content = '<div id="calendar">' . $this->_createNavi() . $this->_createLabels() . '<ul class="dates' . ($weeksInMonth == 6 ? ' row-six' : '') . '">';
		for($i = 0; $i < $weeksInMonth; $i++){
			for($j = 1; $j <= 7; $j++){
				$cellNumber = $i * 7 + $j;
				$content .= $this->_showDay($cellNumber);
			}
		}
		return $content . '</ul></div>';
	}

	private function _createNavi(){
		$nextMonth	= $this->currentMonth == 12	? 1		:	intval($this->currentMonth)	+ 1;
		$nextYear	= $this->currentMonth == 12	?			intval($this->currentYear)	+ 1 : $this->currentYear;
		$preMonth	= $this->currentMonth == 1	? 12	:	intval($this->currentMonth)	- 1;
		$preYear	= $this->currentMonth == 1	? 			intval($this->currentYear)	- 1 : $this->currentYear;
		return
			'<div class="header">' .
				'<a class="prev" href="' . $this->naviHref . '?m=' . sprintf('%02d', $preMonth) . '&y=' . $preYear . '">Prev</a>' .
					'<span class="title">' . date('M. Y', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>' .
				'<a class="next" href="' . $this->naviHref . '?m=' . sprintf("%02d", $nextMonth) . '&y=' . $nextYear . '">Next</a>' .
			'</div>';
	}

	private function _createLabels(){ 
		$content = '';
		foreach($this->dayLabels as $index => $label)
			$content .= '<li class="' . ($label == 6 ? 'end title' : 'start title') . ' title">' . $label . '</li>';
		return '<ul class="labels">' . $content . '</ul>';
	}

	private function _showDay($cellNumber){
		if($this->currentDay == 0){
			if($this->weekStartsOnSunday)
				$firstDayOfTheWeek = date('w', strtotime($this->currentYear . '-' . $this->currentMonth . '-01')) + 1;
			else
				$firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
			if(intval($cellNumber) == intval($firstDayOfTheWeek))
				$this->currentDay = 1;
		}
		if(
				($this->currentDay != 0)
			&&	($this->currentDay <= $this->daysInMonth)
		){
			$this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
			$cellContent = $this->currentDay;
			$this->currentDay++;	
		}else{
			$this->currentDate = null;
			$cellContent = null;
		}
		$cellContent .= '<br/>' . $this->currentDate;
		return '<li id="li-' . $this->currentDate . '" class="' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
			($cellContent == null ? 'mask' : '') . '">' . $cellContent . '</li>';
	}

	private function _weeksInMonth($month = null, $year = null){
		if(null == ($year))
			$year = date('Y', time()); 
		if(null == ($month))
			$month = date('m', time());
		$daysInMonths	= $this->_daysInMonth($month,$year);
		$numOfweeks		= ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);
		if($this->weekStartsOnSunday){
			$monthEndingDay	= date('w', strtotime($year . '-' . $month . '-' . $daysInMonths)) + 1;
			$monthStartDay	= date('w', strtotime($year . '-' . $month . '-01')) + 1;
		}else{
			$monthEndingDay	= date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));
			$monthStartDay	= date('N', strtotime($year . '-' . $month . '-01'));
		}
		if($monthEndingDay < $monthStartDay)
			$numOfweeks++;
		return $numOfweeks;
	}

	private function _daysInMonth($month = null, $year = null){
		if(null == ($year))
			$year = date("Y", time());
 		if(null == ($month))
			$month = date("m", time());
		return date('t', strtotime($year . '-' . $month . '-01'));
	}

}