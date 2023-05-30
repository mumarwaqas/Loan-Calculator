(function ($) {
	$(window).on("load", function () {
		$("#years-box").show();
		var years = $("#loan-number-years").val();
		var YearLabel = "";
		if (years == 1) {
			YearLabel = "Year";
		} else {
			YearLabel = "Years";
		}
		$("#years-t").text(YearLabel);
		$("#years").text(years);
	});

	$(document).on("input change", "#loan-number-years", function () {
		$("#years-box").show();
		var years = $(this).val();
		var YearLabel = "";
		if (years == 1) {
			YearLabel = "Year";
		} else {
			YearLabel = "Years";
		}
		$(this).parent().find("#years-t").text(YearLabel);
		$(this).parent().find("#years").html(years);
		$(this).attr("value", years);

		$("#mc-mortgage-period").val(years);
	});
	
	$(document).on("keyup", "#loan-interest-rate", function (event) {
		var value = $(this).val();

		// Remove non-digit and non-decimal characters
		value = value.replace(/[^0-9.]/g, '');

		// Validate the entered value as a percentage
		var percentageRegex = /^(100|(\d|[1-9]\d)(\.\d{0,2})?)$/;
		if (!percentageRegex.test(value)) {
			$(this).val('');
		}
	});

	$(document).on("keyup", "#loan-total-amount", function (event) {
		var self = $(this);
		self.val(self.val().replace(/[^0-9\.]/g, ''));
		if ((event.which != 46 || self.val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) 
		{
			event.preventDefault();
		}
	});	
	
	$(document).on("click","#click-on-all",function (e) {
		$(".loan-submit").click();
	});

	$(document).on('submit', '.calculation-email', function(e) {
		e.preventDefault(); // prevent the default form submission		
		
		var json_array = [];
		$('.multi-fields').find('.multi-field').find('form').each(function(i, v){
			var data_array = {};
			$(this).find('.field').each(function(i, v){
				if($(this).find('.lable').text() != ""){
					// console.log($(this).find('.lable').text() + " => " + $(this).find('.value').val());
					var label = $(this).find('.lable').text().replaceAll(" ", "_");
					if($(this).find('.value').is(':checked')){
						var value = 'Interest Only';
					}
					else
					{
						var value = $(this).find('.value').val() + $(this).find('.value option:selected').text() + $(this).find('.sign').text();						
					}

					data_array[label] = value;
				}
			});

			var Odata_array = {};
			$(this).find('#loan-output').find('.row').each(function(i, v){
				// console.log($(this).find('div').find('label').text() + " => " + $(this).find('div').find('h4').text());
				var Olabel = $(this).find('div').find('label').text().replaceAll(" ", "_");
				var Ovalue = $(this).find('div').find('h4').text();
				Odata_array[Olabel] = Ovalue;
			});
			
			var output_array = {};
		 		
			output_array['input'] = data_array;
			output_array['output'] = Odata_array;
			//console.log(output_array);
			json_array.push(output_array)
//  		console.log(data);
// 			console.log(json_array);
// 		 	console.log(Odata_array);
			//console.log("=====================================================================================================");
		});
 			// console.log(JSON.stringify(json_array));

			$.ajax({
				type: "POST",
				url: calc_loan_email_send.ajaxurl,
				data: {
					"action": "loan_email_send",
					"name": $("input[name='name']").val(),
					"email": $("input[name='email']").val(),
					"jsonData": json_array,
				},
				beforeSend: function() {
					$(".loader").addClass("loading");
				},
				success: function (data) {
					console.log(data);
				},
				complete: function() {
					alert("Email has been sent.");
					$('form[name="calculation-email"]')[0].reset();
					$(".loader").removeClass("loading");
				},
			});

	});
	
	$(document).ready(function(){		
		$('.multi-field-wrapper').each(function() {
			var $wrapper = $('.multi-fields', this);
			let current_id = 0;
			$(".add-field", $(this)).click(function(e) {
				var id = current_id++;
				if(id < 3)
				$('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).focus();
			});
			$('.multi-field .remove-field', $wrapper).click(function() {
				if ($('.multi-field', $wrapper).length > 1){
					$(this).parent('.multi-field').remove();
				}
			});
		});
	})

	$('#loan-form').on('submit', function (event) {
		event.preventDefault(); // prevent the default form submission
		var $this = $(this);
		$.ajax({
			url: calc_ajax_object.ajaxurl,
			type: "POST",
			data: $($this).serialize() + "&action=loan_calculation",
			success: function (data) {
				// console.log(data);
				$this.find("#loan-output").html(data);
				$('.email-results').addClass('d-block');
				$('.email-results').removeClass('d-none');
				$(".multi-fields").find(".multi-field").find("#loan-form").each(function (i, v) {
					$(".col" + i).html($(this).find(("#loan-output")).html());
					$(".col" + i).removeClass('d-none');
				})
			},
		});
	});

})(jQuery);
