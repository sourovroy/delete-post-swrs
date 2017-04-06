(function($){

	//Precess delete
	$('#start-delete').click(function(event){
		event.preventDefault();

		//Show spiner
		$('.dps-loading > .spinner').addClass('visible');

		//Get data of form
		var formData = {
			action: 'swrs_delete_post_count',
			delete_data: $('#dps-form').serialize()
		};

		//Send ajax request to process data
		$.ajax({
			url: DPS_OBJ.ajax_url,
			//async: false,
			method: 'POST',
			data: formData,
			success: function(response, status, xhr_obj){
				var dataRes = $.parseJSON(response);
				if(dataRes.error == null){
					var result = '<h4>Check your details below</h4>';
					result += '<p>';

					if( dataRes.select_post_type == 'attachment' ){
						result += 'Total Attachments: '+ dataRes.inherit;
						result += '<br>You wish to delete '+ dataRes.post_quantity +' posts';
						result += '<br>Your images will be deleted permanently.';
					}else{
						result += 'Total Publish: '+ dataRes.publish;
						result += '<br>Total Draft: '+ dataRes.draft;
						result += '<br>You wish to delete '+ dataRes.post_quantity +' posts';
					}
					
					result += '</p>';
					result += "<input type='hidden' name='all_delete_data' value='"+ response +"'>";
					result += '<input type="button" id="goto-delete" class="button button-primary" value="Start Delete">';
					$('#dps-show-result').append(result);
					$('.dps-loading > .spinner').removeClass('visible');
				}
				// console.log(dataRes);
			},
			error: function(xhr_obj, status, error){
				console.log(error);
				$('#dps-show-result').append(error);
			}
		});

		//Deisable delete button after one click
		$(this).attr('disabled', 'disabled');

	}); // End of click

	//Final Delete
	$('.wrap').on('click', '#goto-delete', function(event){
		event.preventDefault();

		//Show spiner
		$('.dps-loading > .spinner').addClass('visible');

		//Deisable delete button after one click
		$('#goto-delete').attr('disabled', 'disabled');

		//Get data of form
		var inputData = $('input[name="all_delete_data"]').val(),
		inputDataJson = $.parseJSON(inputData),
		goFormData = {
			action: 'swrs_delete_post_goto',
			delete_data: inputDataJson
		};

		if( inputDataJson.post_quantity != 'all' && inputDataJson.post_quantity < 50 ){
			var loopCount = 1;
		}else{
			var loopCount = Math.ceil(inputDataJson.total_post / 10);
		}
		//Need to fix something here
		//Count not working good
		console.log(loopCount);
		
		/*setTimeout(function(){
			for( var req = 1; req <= loopCount; req++ ){ //Start loop
				
				//Send ajax request to delete post
				$.ajax({
					url: DPS_OBJ.ajax_url,
					async: false,
					method: 'POST',
					data: goFormData,
					success: function(response, status, xhr_obj){
						var dataRes = $.parseJSON(response);
						if(dataRes.error == null){
							var result = '<p>'+ dataRes.deleted +' posts has been deleted.</p>';
							$('#dps-show-result').append(result);
							$('.dps-loading > .spinner').removeClass('visible');
						}
						console.log(response);
					},
					error: function(xhr_obj, status, error){
						console.log(error);
						$('#dps-show-result').append(error);
					}
				});

			}//End of loop
		}, 1000);*/
		
	}); // End of click

})(jQuery);