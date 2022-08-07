$(document).ready(function(){

	var map;
	var marker;
	var addressResults;

	/*
	* v2.0.0
	* auto uppercase for socity field
	*/
	$('#room-title').on('keyup' , function(){
		$(this).val($(this).val().toUpperCase());
	});

	/*
	* v2.0.0
	* mail address must be x.xxx@ajassocies.fr
	*/
	$( "#roomcompany-procedurecontact" ).focusout(function() {

		var mail = $(this).val();

		if (isEmail($(this).val()) && mail.indexOf("@ajassocies.fr") > 0) {
			$(this).parent().find('.hint-block').slideUp();
			$("#create-room-button").attr("disabled", false);
		}
		else{
			$(this).parent().find('.hint-block').slideDown().css('color','red');
			$("#create-room-button").attr("disabled", true);
		}
	});

	/*
	* v2.0.0
	* mail address must be x.xxx@ajassocies.fr & default is admin email provided above
	*/
	$( "#roomcompany-procedurecontact" ).focusout(function() {

		var mail = $(this).val();

		if (isEmail($(this).val()) && mail.indexOf("@ajassocies.fr") > 0) {
			$(this).parent().find('.hint-block').slideUp();
			$("#create-room-button").attr("disabled", false);
		}
		else{
			$(this).parent().find('.hint-block').slideDown().css('color','red');
			$("#create-room-button").attr("disabled", true);
		}
	});

	$('#room-adminid').on('select2:select', function (e) {
		var procedurecontactMail = $(this).select2('data')[0].text;
		var result = procedurecontactMail.match(/\[(.*)\]/);
		$( "#roomcompany-procedurecontact" ).val(result[1]);
	});
	/*
	* @event document.ready +2sec
	*/
	setTimeout(function() {
		var procedurecontactMail = $('#room-adminid').select2('data')[0].text;
		console.log(procedurecontactMail);
		var result = procedurecontactMail.match(/\[(.*)\]/);
		$( "#roomcompany-procedurecontact" ).val(result[1]);
    }, 2000);


	/*
	* v2.0.0
	* Initmap at each modal open instead of at page load
	*/
	$('#address-modal').on('shown.bs.modal', function () {
		var marker = null;
		initMap();
	});

	/*
	* v2.0.0
	* Initmap at each modal open instead of at page load
	*/
	$('#room-archivationdate,#room-publicationdate').change(function(){

		var start = $('#room-publicationdate-disp').val();
		var end = $('#room-archivationdate-disp').val();
		var deadline = getDeadLine(start , 18);

		$('#room-archivationdate,#room-publicationdate').each(function(){
			$(this).parent().find('.help-block').empty();
		});

		if (getFormattedDate(end) > deadline) {

			deadline  = deadline.getDate()+'/'+(deadline.getMonth()+1) + '/' + deadline.getFullYear() ;
			$(this).parent().find('.help-block').empty().append('Date de clôture de la DataRoom ne peut être postérieure au ' + deadline).css('color','red');
		}
		else{
			$(this).parent().find('.help-block').empty();
		}
	});



	/*
	* v2.0.0
	* Jquery helper Tools
	*/


	function getDeadLine(date,months){

		date = getFormattedDate(date);
		var d = date.getDate();
		date.setMonth(date.getMonth()+ +months);
		if (date.getDate() != d) {
			date.setDate(0);
		}
		return date;
	}


	function getFormattedDate(date){

		date = date.split('/');
		// date = date[2]+'-'+date[1]+'-'+date[0];
		date = new Date(date[2] , date[1].replace(/^0+/, '')-1 , date[0]);
		return date;
	}


	function diffDays(start , end){

		start = getFormattedDate(start);
		end = getFormattedDate(end);

		var diff = new Date(end - start);
		var days = diff/1000/60/60/24;

		return days;
	}

	function isEmail(email) {

		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
});