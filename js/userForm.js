function toggleOptionEdit(buttonEl) {
	buttonEl.closest('.options').toggle();
	var toEl = buttonEl.closest('.options').siblings().toggle();
}

function changePassword() {
	if($('#change-password:visible').length > 0) {
		alert('Changing');
		//TODO:
	} 
	$('#change-password').toggle();
	return true;
}