<?php 
/* Kalendář */
class Calendar {
	public $pocetDni;
	public $options = array(
		"month" => 11, 
		"year" => 2023,
		"monthDays" => null,
		"calendarType" => CAL_GREGORIAN, 
		"templatesDirectory" => "./components/", 
		"styleVersion" => "23x11"
	);

	public function __construct( $month = false, $year = false ) {
		$this->options[ "month" ] = date( "m" );
		$this->options[ "year" ] = date( "Y" );
		$this->options[ "styleVersion" ] = date( "dmyhis" );
		//setlocale( LC_ALL, "cs_CZ.UTF-8" );		
		if ( $month ) { $this->options[ "month" ] = $month; }
		if ( $year ) { $this->options[ "year" ] = $year; }
		$this->pocetDni = cal_days_in_month( 
	    	$this->options[ "calendarType" ], 
	    	$this->options[ "month" ], 
	    	$this->options[ "year" ]
	    );

		require_once 'vendor/autoload.php';
		$loader = new \Twig\Loader\FilesystemLoader(
			$this->options[ "templatesDirectory" ]
		);
		$this->twig = new \Twig\Environment($loader);
		$strRepeatFunction = new \Twig\TwigFunction(
			'strRepeat', 
			function ( $strToRepeat, $multiplier ) {
				if ( $strToRepeat && $multiplier > 0 ) { 
					return str_repeat( $strToRepeat, $multiplier );
				}
			}
		);
		$this->twig->addFunction( $strRepeatFunction );
	}

	public function getCalendar( ) {
		$time = strtotime( "{$this->options[ "year" ]}-{$this->options[ "month" ]}-1" );
		$title = strftime( "%B", $time );
		$title.="&nbsp;".$this->options[ "year" ];
		$calendar = $this->renderTemplate( 
			"calendar.html", 
			[ 
				"title" => $title, 
				"month" => $this->options[ "month" ], 
				"year" => $this->options[ "year" ], 
				"daysInMonth" => $this->pocetDni,
				"days" => $this->ziskejDnyVMesici(
					$this->options[ "month" ], 
					$this->options[ "year" ]
				)
			] 
		);
		$layout = $this->renderTemplate( 
			"layout.html", [
				"body" => $calendar, 
				"styleVersion" => $this->options[ "styleVersion" ]
			]
		);
		return $layout;
	}

	public function renderTemplate( $templateFile, $templateData ) {
		return $this->twig->render( $templateFile, $templateData );
	}


	/* vrátí pole dnů podle měsíce & roku */
	public function ziskejDnyVMesici( $cisloMesice, $cisloRoku ) {
		$dnyVMesici = array( );

	    for ( $den = 1; $den <= $this->pocetDni; $den++ ) {
	        $datum = "$cisloRoku-$cisloMesice-$den";
	        $denVTydnu = date( 'N', strtotime( $datum ) );
	        $dnyVMesici[ $den ] = array(
	            'den' => $den,
	            'datum' => $datum,
	            'den_v_tydnu' => $denVTydnu, 
	            "events" => $this->getEventsByDate( $datum )
	        );
	    }

	    return $dnyVMesici;
	}

	/* vrati události pro konkrétní den */
	function getEventsByDate( $date ) {
		$events = array( );

		if ( rand( 1, 10 ) > 5 ) {
			$events[] = array(
				"title" => 'test event',
				"date" => $date,
				"time" => "12:00",
				"type" => "sport"
			);
		}
		
		return $events;
	}	
	
}

function get( $varName ) {
	if ( isset( $_GET[$varName] ) ) {
		return htmlspecialchars( $_GET[ $varName ] );
	}
	return false;
}

//$month = (get("month") ? get("month") : 4; // květen
//$cisloRoku = 2023;
$month = get("month");
$year = get("year");
$calendar = new calendar( $month, $year );
echo $calendar->getCalendar( );

//$days = ziskejDnyVMesici( $cisloMesice, $cisloRoku );
//$monthAndYear = strftime( "%B", strtotime( "{$cisloRoku}-{$cisloMesice}-1" ) )." ".$cisloRoku;
// Výpis výsledku
//print_r($days);
/*
echo '<div class="calendar">';
echo '<div class="calendar-header">
	<div class="calendar-header-nav">&lt;</div>
	<div class="calendar-header-month">'.$monthAndYear.'</div>
	<div class="calendar-header-nav">&gt;</div>
</div>
<div class="calendar-header">
	<div class="calendar-header-label">PO</div>
	<div class="calendar-header-label">ÚT</div>
	<div class="calendar-header-label">ST</div>
	<div class="calendar-header-label">ČT</div>
	<div class="calendar-header-label">PÁ</div>
	<div class="calendar-header-label">SO</div>
	<div class="calendar-header-label">NE</div>
</div>';
foreach ($days as $key => $day) {
	if ( $key == 1 ) { 
		echo str_repeat( 
			'<div class="calendar-day calendar-day-empty"></div>', 
			( $day["den_v_tydnu"] - 1 )
		); 
	}
	$events = json_encode($day["events"]);
	echo '<div class="calendar-day" data-events="'.$events.'">'.$day["den"].'</div>';
	if ( $day["den_v_tydnu"] == 7 ) { 
		echo '<div class="calendar-break"></div>'; 
	}
}
echo "</div>";
*/
?>
