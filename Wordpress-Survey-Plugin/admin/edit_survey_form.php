<style>h2 {display:inline-block; float:left; margin-top:3px; margin-right:20px;} #success_msg{clear:both;} 
input.question_order
{
	    width: 50px;
    text-align: center;
    float: right;
    margin-right: 11px;
}
input.answer_order
{
	    width: 50px;
    text-align: center;
    margin-right: 11px;
}
</style><div class="wrap">
        <h2><i style="font-size:30px; padding-right:10px;" class="dashicons 
dashicons-plus-alt"></i> Edit Survey Form</h2> 
<div id="success_msg" class="updated notice notice-success is-dismissible" style="display:none"><p>Survey Form is updated successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
		<form  id="edit_survey_form" onsubmit="return submit_form()">
			<input type="hidden" name="action" value="update_survey_form" />
			 <?php 
			 	global $wpdb; 
			 	$sql="select * from az_survey_forms where id=".$_GET['id'];
			 	$row= $wpdb->get_results($sql);


			 ?>
			<input type="hidden" name="survey_form_id"  value="<?php echo $_GET['id']; ?>" />
			<input class="large-text code" style="height:45px; padding-left:10px; padding-right:10px;" type="text" value="<?php echo $row[0]->title; ?>" name="title" placeholder="Title" />
			<a href="#" style="float:right" class="add_question_btn">+ Add Question</a>
			<div id="questions_container">
				<?php 

				$sql="select * from az_survey_questions where survey_id=".$_GET['id']." order by orders asc";

				$rows= $wpdb->get_results($sql);
				$question_number = 0;
				foreach($rows as $row)
				{
					$question_number++;
				?>

				<div class="question">
					<input type="hidden" value="<?php echo $row->id; ?>" name="question_id[]" />
					<br><br><hr><h2>Question <?php echo $question_number; ?> </h2> <a href="javascript:void(0);"  style="text-decoration:none" onclick="delete_question(this)"><span class="dashicons dashicons-trash"></span></a>
|
					<a title="Duplicate" onclick="duplicate_question(event, this)" href="javascript:void(0);"  style="text-decoration:none" onclick="delete_question( this)"><span class="dashicons dashicons-admin-page"></span></a> 
					<input type="text" value="<?php echo $row->orders; ?>" name="question_order[]" class="question_order">

					<textarea  class="large-text code" style="height:150px" name="questions[]" placeholder="Type your Question here" ><?php echo stripslashes($row->question); ?></textarea> 
					
					<div class="answers_container">
						<?php 
						$sql="select * from az_survey_answers where question_id=".$row->id." order by orders asc";

						$answers= $wpdb->get_results($sql);
						$answer_number = 0;

						foreach($answers as $answer):
						$answer_number++; ?>
					
						<div class="answer">

							<input type="hidden" value="<?php echo $answer->id; ?>" name="answer_id[<?php echo $question_number-1; ?>][]" />
							<h4>Answer <?php echo $answer_number; ?>:</h4>
							<select onchange="" name="answer_types[<?php echo $question_number-1; ?>][]">
								<option value="single" <?php echo ($answer->answer_type=="single")?"selected":""; ?>>Single</option>
								<option value="multiple" <?php echo ($answer->answer_type=="multiple")?"selected":""; ?>>Multiple</option>
								<option value="open" <?php echo ($answer->answer_type=="open")?"selected":""; ?>>Open</option></select>
							<input type="text" value="<?php  echo stripslashes($answer->answer); ?>" class="" name="answers[<?php echo $question_number-1; ?>][]" />

							<a href="javascript:void(0);" style="text-decoration:none" onclick="delete_answer(this)"><span class="dashicons dashicons-trash"></span></a> <input type="text" value="<?php echo $answer->orders ?>" name="answer_order[<?php echo $question_number-1; ?>][]" class="answer_order">
						</div>
						<?php endforeach; ?>
					</div>

					<a href="#" style="float:right" class="add_answer_btn">+ Add Answer</a>
				</div>
				<?php 
				}
				?>
			</div><br>
			<a href="#" style="float:right" class="add_question_btn">+ Add Question</a>
			<?php echo submit_button(); ?>
		</form>
    </div>
    <script>
    $ = jQuery;
    var ajax_url 		= '<?php echo admin_url('admin-ajax.php'); ?>';
    function submit_form()
	{
		
		// hide success msg whenever submit is pressed
		$("#success_msg").fadeOut();
 
		// collectiong data
		var name 			= $("input[name='title']").val();
	
		 // this is required url for ajax calls in the wordpress
        
 
        var formData = new FormData($("#edit_survey_form")[0]);
 
		$.ajax({
			url:ajax_url,
			type:"POST",
			data:formData,
			cache:false,
			contentType:false,
			processData:false
		}).done(function(responseTxt){
			if(responseTxt!='1')
			{
				alert(responseTxt);
			}
			else 
			{
				//   $("html, body").animate({ scrollTop: 0 }, "slow");

				// $("#success_msg").fadeIn(); 

				// setTimeout(function(){$("#success_msg").fadeOut(); }, 2000);

				location.reload();
			}
			
			
		});
		return false;
	}
	var delete_answer_link ='<a href="javascript:void(0);" style="text-decoration:none" onclick="delete_answer(this)"><span class="dashicons dashicons-trash"></span></a> ';

	var delete_question_link ='<a href="javascript:void(0);" style="text-decoration:none" onclick="delete_question(this)"><span class="dashicons dashicons-trash"></span></a> | <a title="Duplicate" onclick="duplicate_question(event, this)" href="javascript:void(0);"  style="text-decoration:none" onclick="delete_question( this)"><span class="dashicons dashicons-admin-page"></span></a>';

	
	$(".add_question_btn").click(function(e){
		e.preventDefault();
		var total_questions = parseInt($('.question').length);
		var question = $('<div class="question"><input type="hidden" value="0" name="question_id[]" /><br><br><hr><h2>Question '+(total_questions+1)+' </h2>'+' '+delete_question_link+'   <input type="text" value="0" name="question_order[]" class="question_order"> <?php echo preg_replace('/(\r\n|\n|\r)/', '', '
					<textarea  class="large-text code" style="height:150px" name="questions[]" placeholder="Type your Question here" ></textarea>
					
					<div class="answers_container">
						
					</div>
					<a href="#" style="float:right" class="add_answer_btn">+ Add Answer</a>
				</div>'); ?>');
		$("#questions_container").append(question);
		question.children('.question_order').val((question.index()+1));
		$('body, html').animate({ scrollTop: $(question).offset().top }, 1000);
		$(question).find('textarea').focus();



	
	});

		$("body").on('click', '.add_answer_btn',function(e){

		e.preventDefault();
		var question_index =$(this).closest('.question').index(); 
		// alert($(this).closest('.question').find('.answers_container').html());
		var total_answers = parseInt($(this).closest('.question').find('.answers_container .answer').length);//alert(total_answers);
		var answer = $('<div class="answer">'+'<h4>Answer '+(total_answers+1)+':</h4><input type="hidden" name="answer_id['+question_index+'][]" value="0" /><select onchange="" name="answer_types['+question_index+'][]"><option value="single">Single</option><option value="multiple">Multiple</option><option value="open">Open</option></select><input type="text" class="" name="answers['+question_index+'][]" />'+delete_answer_link+' <input type="text" value="0" name="answer_order['+question_index+'][]" class="answer_order"></div>');
		$(this).closest('.question').find('.answers_container').append(answer);
		answer.children('.answer_order').val((answer.index()+1));
		$('body, html').animate({ scrollTop: $(answer).offset().top }, 1000);
		$(answer).find('input[type=text]:first').focus();
	});
	

	function delete_answer(link)
	{
		var question_index = $(link).closest('.question').index();
		var answer = $(link).closest('.answer');
		var answer_id = answer.find('input[name="answer_id['+question_index+'][]"]').val();
		if(answer_id!='0')
		{
			$.ajax({
				data:{answer_id:answer_id, action:'delete_survey_answer'},
				type:"POST", 
				url:ajax_url
			}).done(function(response){
				response = JSON.parse(response);
				if(response.status=='1')
				{
					remove_html_answer(question_index, $(link).closest('.answer'), link);
				}
				else
				{
					console.log(response.data);
				}
				
				
			});
		}
		else
		{
			remove_html_answer(question_index, $(link).closest('.answer'), link);
		}

		function remove_html_answer(question_index, answer, link)
		{
			$(link).closest('.answer').slideUp();
			setTimeout(function(){

					answer.remove();
					$('.question:nth-child('+(question_index+1)+')').find('.answer').each(function(index){
						
						$(this).children('h4').html('Answer '+(index+1)+": ");
					});
				}, 400);
		}
		
	}


	function delete_question(link)
	{
		var question = $(link).closest('.question');
		var question_id = question.find('input[name="question_id[]"]').val();
		var question_index = $(link).closest('.question').index();
		if(question_id!='0')
		{
			$.ajax({
				data:{question_id:question_id, action:'delete_survey_question'},
				type:"POST", 
				url:ajax_url
			}).done(function(response){
				response = JSON.parse(response);
				if(response.status=='1')
				{
					remove_html_question(question_index, $(link).closest('.question'), link);
				}
				else
				{
					console.log(response.data);
				}
				
				
			});
		}
		else
		{
			remove_html_question(question_index, $(link).closest('.question'), link);
		}

		function remove_html_question(question_index, question, link)
		{
			question.slideUp();
			setTimeout(function(){

					question.remove();
					re_order_questions();
				}, 400);
		}
		
	}


	function re_order_questions()
	{
		$('.question').each(function(questionIndex){

			var question = $(this);

			question.children('h2').html('Question '+(questionIndex+1)+" ");

			
			question.find('.answer input[name*="answers"]').each(function(index){
				$(this).attr('name', 'answers['+(questionIndex)+'][]');
			});

			question.find('.answer input[name*="answer_id"]').each(function(index){
				$(this).attr('name', 'answer_id['+(questionIndex)+'][]');
			});


			question.find('.answer select').each(function(index){
				$(this).attr('name', 'answer_types['+(questionIndex)+'][]');
			});


			question.find('.answer .answer_order').each(function(index){
				$(this).attr('name', 'answer_order['+(questionIndex)+'][]');
			});

		});
	}


	function duplicate_question(e, link)
	{
		e.preventDefault();

		var question = $(link).closest('.question').clone();
		question.find('input[name="question_id[]"]').val(0);
		
		$('#questions_container').append(question);

		var new_question_order = parseInt(question.children('.question_order').val())+1;
		question.children('.question_order').val(new_question_order);

		question.find('.answer input[name*="answer_id"]').each(function(index){
				$(this).attr('name', 'answer_id['+(index)+'][]');
				$(this).val(0);
			});
		re_order_questions();


		$('body, html').animate({ scrollTop: $(question).offset().top }, 1000);
		re_order_questions();

	}


	
	</script>