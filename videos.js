/**
* Checks the add vid form for empty values
* @return {bool} - True - Everything is good.
*                  False - A value is missing.
*/
function checkAddVidFields() {
  var vidName = document.getElementById("vidName").value;
  var vidCat = document.getElementById("vidCat").value;
  var vidLen = document.getElementById("vidLen").value;
  var msg = "Please fill in the following fields:";
  var showMsg = false;

  if (vidName === '') {
    msg += ' Video Name';
    showMsg = true;
  }

  if (vidCat === '') {
    if (showMsg) {
      msg += ',';
    }
    msg += ' Video Category';
    showMsg = true;
  }

  if (vidLen === '') {
    if (showMsg) {
      msg += ',';
    }
    msg += ' Video Length';
    showMsg = true;
  }

  if (showMsg) {
    alert(msg);
    return false;
  }

  return true;
}

function deleteVid() {
  alert("Need delete vid implementation");
}