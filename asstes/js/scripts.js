(function($){
	$(document).ready(function(){

		/**
		 * Precess delete
		 */
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


		/**
		 * Delete post ajax all
		 */
		var currentLoop = 0,
			loopCount = 0;

		// Remove sessionStorage delete item on refresh
		if(sessionStorage.deleteItem){
		    sessionStorage.removeItem("deleteItem");
		}

		// Delete post AJAX function 
		function delete_post_ajax_call(goFormData){
			if(currentLoop < loopCount){
		 		$.ajax({
					url: DPS_OBJ.ajax_url,
					// async: false,
					method: 'POST',
					data: goFormData,
					success: function(response, status, xhr_obj){
						var dataRes = $.parseJSON(response);
						if(dataRes.error == null){
							currentLoop++;
							var result = '<p>'+ dataRes.deleted_ids +' posts has been deleted.</p>';
							$('#dps-show-result').append(result);

							//Count how may post delete
							if(sessionStorage.deleteItem){
							    sessionStorage.deleteItem = Number(sessionStorage.deleteItem) + Number(dataRes.deleted);
							}else{
							    sessionStorage.deleteItem = Number(dataRes.deleted);
							}

							//Recall the function for ajax request
							delete_post_ajax_call(goFormData);
						}else{
							$('#dps-show-result').append('<p>Error: '+ dataRes.error +'</p>');
						}
					},
					error: function(xhr_obj, status, error){
						console.log(error);
						$('#dps-show-result').append('<p> Error: '+error+'</p>');
					}
				});
	 		}else{
	 			$('.dps-loading > .spinner').removeClass('visible');
	 			$('#dps-show-result').append("<p>Delete has been completed.</p>");
	 			$('#dps-show-result').append("<p>Total "+ sessionStorage.deleteItem +" posts deleted.</p>");
				// Remove sessionStorage delete item after show
				if(sessionStorage.deleteItem){
				    sessionStorage.removeItem("deleteItem");
				}
	 		}
		}// End of delete_post_ajax_call


		/**
		 * Final Delete
		 */
		$('.wrap').on('click', '#goto-delete', function(event){
			event.preventDefault();

			//Show spiner
			$('.dps-loading > .spinner').addClass('visible');

			//Deisable delete button after one click
			$('#goto-delete').attr('disabled', 'disabled');

			//Get data of form
			var inputData = $('input[name="all_delete_data"]').val(),
			inputDataJson = $.parseJSON(inputData);

			if( inputDataJson.post_quantity != 'all' && inputDataJson.post_quantity < 50 ){ //If post quentity is less then 50
				loopCount = 1;
				inputDataJson.prePage = inputDataJson.post_quantity;

			}else if( inputDataJson.post_quantity != 'all' && inputDataJson.post_quantity >= 50  && inputDataJson.total_post < inputDataJson.post_quantity ){ // If total post is samller then your selected post quantity
				loopCount = Math.ceil(inputDataJson.total_post / 10);
				inputDataJson.prePage = 10;

			}else if( inputDataJson.post_quantity != 'all' && inputDataJson.post_quantity >= 50 ){ // if post quantity is greater then 50
				loopCount = Math.ceil(inputDataJson.post_quantity / 10);
				inputDataJson.prePage = 10;

			}else if( inputDataJson.post_quantity == 'all' ){ // Count loop for all post
				loopCount = Math.ceil(inputDataJson.total_post / 10);
				inputDataJson.prePage = 10;
			}

			$('#dps-show-result').append('<p>It will make ' + loopCount + ' request to delete post.</p>');

			var goFormData = {
				action: 'swrs_delete_post_goto',
				delete_data: inputDataJson
			};

			// console.log(currentLoop);

			delete_post_ajax_call(goFormData);
			
		}); // End of click

	// End of document ready
	});
})(jQuery);