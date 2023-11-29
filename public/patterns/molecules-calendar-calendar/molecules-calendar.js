class Calendar {
  	constructor( x ) {
		console.log( "calendar" );
		this.attachHandlers( );
	}

	attachHandlers( ) {
		this.bindDayClick( );

	}

	bindDayClick( ) {
		console.log( "calendar.bindDayClick()" );
		
	}
}
//export default Calendar;
//const calendar = new Calendar();
//exports = calendar;
//exports = { Calendar };
//module.exports = { Calendar };