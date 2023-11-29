import Calendar from '../_patterns/molecules/calendar/calendar.js';
//import $ from 'jquery';
class Main {
	constructor( ) {
		console.log( "Main" );
		const calendar = new Calendar( );
		calendar.init( );
	}
}
new Main();