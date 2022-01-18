$('#csv-script').on('click', function(){
	$.ajax({
		url: 'app.php',
		success: function() {
		 alert('Success')
		}
	 });
	})