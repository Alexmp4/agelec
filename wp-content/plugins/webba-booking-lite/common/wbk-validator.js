// WEBBA Booking js validation functions

// check integer
function wbkCheckInteger( val ) { 
 	return /^\+?(0|[1-9]\d*)$/.test(val);
}
// check integer
function wbkCheckFloat( val ) { 
 	return /^(?:[1-9]\d*|0)?(?:\.\d+)?$/.test(val);
}

// check string 
function wbkCheckString( val, min, max ) {
	if ( val.length < min || val.length > max ) {
		return false;
	} else {	
		return true; 
	}
}
// check email
function wbkCheckEmail( val ) {
var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
if ( val == '' || !re.test(val) ){    
    return false;
}
return true;

}
// check interger range
function wbkCheckIntegerMinMax( val, min, max ) { 
    if ( val < min || val > max ) {
		return false;
	} else {
		return true;
	}
}
// check phone
function wbkCheckPhone( val ) {
	var pattern = new RegExp(/^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/);
	return pattern.test(val);
}
// check price
function wbkCheckPrice( val ) {
	if(  val == '' ){
		return false;
	}
	if( wbkCheckInteger( val ) ){
		if ( wbkCheckIntegerMinMax( val, 0, 9999999 ) ){
			return true;
		}	
	}
	if( wbkCheckFloat( val ) ){
		if ( val >= 0 || val <= 9999999 ) {
			return true;
		}	
	}
	return false;
}