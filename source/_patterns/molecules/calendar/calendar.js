export default class Calendar {
	options = {
		"rootElement": ".calendar",
		"dayElement": ".calendar .calendar-day",
		"eventsElement": ".calendar .calendar-events"
	}
	
  	constructor( ) {
		console.log( "calendar" );			
	}

	init( ) {
		console.log( "calendar.init()", jQuery );	
		this.attachHandlers( );
	}

	attachHandlers( ) {
		console.log( "calendar.attachHandlers()" );	
		const _this = this;
		jQuery(document).ready(function (){
			_this.bindDayClick( );
		});
		htmx.onLoad( function( elt ) {
			_this.bindDayClick( );
		});

	}

	bindDayClick( ) {
		console.log( "Calendar.bindDayClick( )" );
		const _this = this;
		jQuery( this.options.dayElement ).on( "click touch", function ( ) {
			console.log( "clicked", $( this ) );
			_this.showDayEvents( $( this ).attr( "data-events" ) );
		})
	}

	showDayEvents( eventData ) {
		console.log( "Calendar.showDayEvents( )", eventData );
		const _this = this;
		let jsonData = JSON.parse( eventData );
		if ( Array.isArray( jsonData ) && jsonData.length ) {
			jsonData.forEach( function ( value, index ) {
				let date = new Date(Date.parse(value.date));
				let dateFormated = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear()+"&nbsp;"+value.time;
				$(_this.options.eventsElement).html(
					$('<p>').html( '<strong><i class="bi bi-calendar3"></i>'+value.title+" | "+dateFormated+"</strong>"+value.text )
				);
			});
		} else {
			$(_this.options.eventsElement).html("");
		}
	}
}
