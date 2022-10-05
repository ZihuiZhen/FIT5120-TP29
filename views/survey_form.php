<?php
	global $wpdb;
	$survey_form = $wpdb->get_row("select * from az_survey_forms where id='$id'");
	//wp_enqueue_script( 'test-scripte', plugins_url(). '/Wordpress-Survey-Plugin/js/test.js', false );
	wp_enqueue_script( 'jquery-script', 'https://code.jquery.com/jquery-2.2.4.min.js');
	wp_enqueue_script( 'test-script', plugins_url(). '/Wordpress-Survey-Plugin/js/survey.js', false );

	wp_enqueue_style('main-styles', plugins_url() . '/Wordpress-Survey-Plugin/css/style.css');
?>
<style>

/*
[id^="question-"] {
	display: none;
}

[id^="question-"].active {
	display: block;
}
*/


#success_msg
{
	width:100%;
	padding:10px 20px; background-color:green; color:#FFF;
}
</style>
<form action="" id="survey_form">
	<input type='hidden' name='action'	value='save_user_submission' />
	<h1><?php echo $survey_form->title; ?></h1>

	<?php 
		$questions = $wpdb->get_results("select * from az_survey_questions where survey_id='$id'"." order by orders asc");

		foreach($questions as $question_index => $question)
		{
			?>

			<div class="question" id="question-<?= $question->id ?>">

			<?php
			echo "<input type='hidden' name='question_ids[]' value='".$question->id."' /><h3> ".($question_index+1).": ".stripslashes($question->question)."</h3>";

			$answers = $wpdb->get_results("select * from az_survey_answers where question_id='$question->id' order by orders asc");

			foreach($answers as $answer_index =>$answer)
			{
			?>
			<div class="answer">
			<?php
				if($answer->answer_type=='single' || $answer->answer_type=='multiple' )
				{
					$type = ($answer->answer_type=='single')?'radio':'checkbox';
					echo " <input type='".$type."' value='".$answer->id."' name='answers[question_".$question_index."][]' /> ".$answer->answer."<br>";
				}
				else
				{
					echo "<input type='hidden' name='answer_ids[question_".$question_index."][]' value='".$answer->id."' />";
				}
			?>
			</div>
			<?php
			}
		?>
			</div>

		<?php
		}


	?>
	<br><br>
	<input id="submitSurvey" type="submit" value="Submit" />
</form>

<script>
$ = jQuery;
$('#survey_form').on('submit', function(e){
	var answered=0;
	// validation
	$("#survey_form .question").each(function(question_index){
		 answered=0;
		$(this).find('.answer').each(function(index){
			if($(this).find("input[type=radio]").is(':checked'))
			{

				answered++;
			}
			if($(this).find("input[type=text]").val())
			{

				answered++;
			}

			if($(this).find("input[type=checkbox]").is(':checked'))
			{
				
				answered++;
				
			}

		});


	});
		if(answered<1)
		{
			alert('U heeft niet alle vragen beantwoord.');
			return false;
		}

    var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
	e.preventDefault();
	var formData = new FormData($('#survey_form')[0]);

	$.ajax({
		data:formData,
		url:ajax_url,
		type:"POST",
		cache:false,
		processData:false,
		contentType:false

	}).done(function(response){
		if(response=='1')
		{
			//$("#survey_form").html("<div id='success_msg'>Bedankt voor uw deelname!</div>");
			
			// Simulate a mouse click:
			window.location.href = "https://activitiesforcarers.cf/activity-suggestions/";
		}
		else
		{
			alert("Submission Failed..Response: "+response);
		}
	});
});

$(".answer input[type='text']").on('keyup',function(){

$(this).closest('.question').find('.answer').each(function(index){
     $(this).find('input[type="radio"]').prop('checked', false);
});

});


$(".answer input[type='radio']").on('click',function(){

	$(this).closest('.question').find('.answer').each(function(index){
		$(this).find('input[type="text"]').val('');
	});
});
</script>