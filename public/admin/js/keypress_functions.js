function isNumberKey(evt) {      //onkeypress="return isNumberKey(event)"
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }
}

function isCharacterKey(evt) {      //onkeypress="return isCharacterKey(event)"
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 65 || charCode > 90) &&
        (charCode < 97 || charCode > 122 ) && charCode !=32 ){
        return false;
    } else {
        return true;
    }
}


function isSpecialCharacter(evt) {      //onkeypress="return isSpecialCharacter(event)"
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 65 || charCode > 90) &&
        (charCode < 97 || charCode > 122 ) && charCode !=36  && charCode !=32 ){
        return false;
    } else {
        return true;
    }
}

function isCharacterKeySpecial(evt) {      //onkeypress="return isCharacterKeySpecial(event)"
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 65 || charCode > 90) &&
        (charCode < 97 || charCode > 122 ) && charCode !=32 &&
        charCode != 40  && charCode != 41 && charCode != 45){
        return false;
    } else {
        return true;
    }
}




function onlyPositiveNumberKey(evt) {      //onkeypress="return onlyPositiveNumberKey(event)"
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ( charCode > 31 && (charCode < 49 || charCode > 57)) {
        return false;
    } else {
        return true;
    }
}

function onlyRoundNumber(evt) {      //onkeypress="return onlyRoundNumber(event)"

    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }

}

function isPhoneNumber(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    if ( charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }
}

