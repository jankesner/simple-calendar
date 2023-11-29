<?php 
/* 
	Simple calendar demo
	---
	Author: jan kesner 
	Date: 11/2023
*/
class Calendar {
	public $daysInMonth;
	public $options = array(
		"month" => 11, 
		"year" => 2023,
		"monthDays" => null,
		"calendarType" => CAL_GREGORIAN, 
		"templatesDirectory" => "./components/", 
		"cacheBuster" => ""
	);

	public function __construct( ) {
		setlocale( LC_ALL, "cs_CZ.UTF-8" );	
		require_once 'vendor/autoload.php';
		$loader = new \Twig\Loader\FilesystemLoader(
			$this->options[ "templatesDirectory" ]
		);
		$this->twig = new \Twig\Environment( $loader );
		$strRepeatFunction = new \Twig\TwigFunction(
			'strRepeat', 
			function ( $strToRepeat, $multiplier ) {
				if ( $strToRepeat && $multiplier > 0 ) { 
					return str_repeat( $strToRepeat, $multiplier );
				}
			}
		);
		$this->twig->addFunction( $strRepeatFunction );
		$this->options[ "cacheBuster" ] = date( "dmyhis" );
	}

	public function getCalendar( $year, $month ) {
		if ( intval( $month ) ) { $this->options[ "month" ] = $month; }
		if ( intval( $year ) ) { $this->options[ "year" ] = $year; }
		if ( $this->options[ "month" ] == 1 ) { 
			$prevMonth = 12; 
			$prevYear = ( $this->options[ "year" ] - 1 ); 
			$nextMonth = ( $this->options[ "month" ] + 1 );
			$nextYear = $this->options[ "year" ];
		} else if ( $this->options[ "month" ] == 12 ) { 
			$prevMonth = ( $this->options[ "month" ] - 1 ); 
			$prevYear = $this->options[ "year" ];
			$nextYear = ($this->options[ "year" ] + 1 );
			$nextMonth = 1;
		} else {
			$prevMonth = ( $this->options[ "month" ] - 1 ); 
			$prevYear = $this->options[ "year" ];
			$nextYear = $this->options[ "year" ];
			$nextMonth = ( $this->options[ "month" ] + 1 );
		}
		if ( $this->options[ "month" ] == 1 ) { $prevMonth = 12; }
		$this->daysInMonth = cal_days_in_month( 
	    	$this->options[ "calendarType" ], 
	    	$this->options[ "month" ], 
	    	$this->options[ "year" ]
	    );
		$time = strtotime( "{$this->options[ "year" ]}-{$this->options[ "month" ]}-1" );
		$title = strftime( "%B", $time )."&nbsp;".$this->options[ "year" ];
		$calendar = $this->renderTemplate( 
			"calendar.html", 
			[ 
				"title" => $title, 
				"month" => $this->options[ "month" ], 
				"year" => $this->options[ "year" ], 
				"daysInMonth" => $this->daysInMonth,
				"days" => $this->getDaysOfMonth(
					$this->options[ "month" ], 
					$this->options[ "year" ]
				),
				"linkPrev" => "calendar.php?month={$prevMonth}&year={$prevYear}",
				"linkNext" => "calendar.php?month={$nextMonth}&year={$nextYear}"
			] 
		);
		$layout = $this->renderTemplate( 
			"layout.html", [
				"body" => $calendar, 
				"cacheBuster" => $this->options[ "cacheBuster" ]
			]
		);
		return $layout;	
	}

	public function renderTemplate( $templateFile, $templateData ) {
		return $this->twig->render( $templateFile, $templateData );
	}

	public function getDaysOfMonth( $month, $year ) {
		$days = array( );

	    for ( $day = 1; $day <= $this->daysInMonth; $day++ ) {
	        $date = "{$year}-{$month}-{$day}";
	        $dayOfWeek = date( 'N', strtotime( $date ) );
	        $days[ $day ] = array(
	            'day' => $day,
	            'date' => $date,
	            'dayOfWeek' => $dayOfWeek, 
	            "events" => $this->getEventsByDate( $date )
	        );
	    }

	    return $days;
	}

	function getEventsByDate( $date ) {
		$events = array( );

		if ( rand( 1, 10 ) > 5 ) {
			$events[ ] = array(
				"title" => $this->lorem( 2 ),
				"date" => $date,
				"time" => "12:00",
				"type" => "sport",
				"text" => $this->lorem( 10 )
			);
		}
		
		return json_encode( $events );
	}

	function lorem( $numberOfWords = 10 ) {
		$numberOfWords = intval( $numberOfWords );
		$out = "";
		$lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
		$words = explode( " ", $lorem );
		for ( $i=0; $i<$numberOfWords; $i++ ) {
			$out.=$words[ rand( 0, count( $words ) - 1 ) ]." ";
		}
		return $out;
	}
	
}

function get( $varName ) {
	if ( isset( $_GET[ $varName ] ) ) {
		return htmlspecialchars( $_GET[ $varName ] );
	}
	return false;
}

$month = get( "month" );
$year = get( "year" );
$calendar = new calendar( );
echo $calendar->getCalendar( $year, $month );
?>
