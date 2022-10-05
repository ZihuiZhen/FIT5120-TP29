<?php 
    /*
    Plugin Name: Survey Plus
    Description: Plugin for showing and creating survey form
    Author: Ayyaz Zafar
    Version: 1.6
    Author URI: http://www.AyyazZafar.com


    */

add_action('admin_menu', 'az_survey_form_menu');

/**
 * Add admin menu list
 */
function az_survey_form_menu() {

	// Add top level menu
    add_menu_page("Survey Forms", "Survey Forms", 'manage_options', "survey-plus", "sfp_main_page");

	// Add sub menus
    add_submenu_page (null,  'edit survey', 'edit survey', 1, 'edit-az-survey-form', "sfp_edit_survey_form");
    add_submenu_page ('survey-plus',  'Add New', 'Add New', 1, 'add-new-az-survey-form', "spf_new_survey_form");
	add_submenu_page ('survey-plus',  'Categories', 'Categories', 1, 'az-survey-categories', "spf_categories");
	add_submenu_page ('survey-plus',  'Activities', 'Activities', 1, 'az-survey-activities', "spf_activities");
	add_submenu_page ('survey-plus',  'Settings', 'Settings', 1, 'az-survey-settings', "spf_settings");
}

function spf_categories()
{
	include("admin/categories.php");
}

function spf_settings()
{
	include("admin/settings.php");
}

function spf_activities()
{
	include("admin/activities.php");
}

function spf_edit_survey_form()
{

}

function spf_new_survey_form()
{
	include("admin/new_survey_form.php");
}

function submit_survey_form()
{
	$title = $_POST['title'];

	 global $wpdb; // this is required so that you can use wordpress to execute your sql queries

	 // inserting survey form
  	$sql="insert into  az_survey_forms (title) values('$title')";

  	$result= $wpdb->insert('az_survey_forms', array('title'=>$title));
  	echo $wpdb->last_error;
  	$survey_id =  $wpdb->insert_id;
  	if($result)
  	{
  		$arrQuestions = $_POST['questions'];
		///print_r($_POST); die;
		foreach($arrQuestions as $question_index => $question)
		{
			// inserting questions in database
			$question_order = $_POST['question_order'][$question_index]; 
  			$result= $wpdb->insert('az_survey_questions', array('survey_id'=>$survey_id, 'question'=> $question, 'orders'=>$question_order));
  			$question_id =  $wpdb->insert_id;
  			echo $wpdb->last_error;

  			$arrAnswers = $_POST['answers'];
  			if($result)
  			{
  				if(isset($arrAnswers[$question_index]))
  				{
  					foreach($arrAnswers[$question_index] as $answer_index => $answer)
					{
						$answer_type = $_POST['answer_types'][$question_index][$answer_index];
						$answer_order = $_POST['answer_order'][$question_index][$answer_index]; 

						$sql="insert into  az_survey_answers (question_id, answer, answer_type) values($question_id, '$answer', '$answer_type')";
	  					$result= $wpdb->insert('az_survey_answers', array('question_id'=>$question_id, 'answer'=> $answer, 'answer_type'=>$answer_type, 'orders'=>$answer_order));
	  					echo $wpdb->last_error;
					}
  				}

				

				
  			}

  			else
  			{
  				echo "Question could not be inserted in database";
  			}
			


		}

		
  	}


  	if($result)
	{
			echo 1;
			wp_die();
	}
	else
	{
		echo "failed";
		wp_die();
	}
	
}

add_action('wp_ajax_submit_survey_form', 'submit_survey_form');

function sfp_main_page()
{

	include("admin/home.php");
}
 


/**
 * Create/Update tables
 */
function activate_process() {

    global $wpdb; 

	// Create the survey answers
	$sql1="CREATE TABLE IF NOT EXISTS `az_survey_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_type` varchar(10) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `votes` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `orders` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

// Create the survey forms
$sql2 = "CREATE TABLE IF NOT EXISTS `az_survey_forms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

// Create the survey questions
$sql3 = "CREATE TABLE IF NOT EXISTS `az_survey_questions` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `orders` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$sql4 = "ALTER TABLE `az_survey_questions`
  ADD PRIMARY KEY (`id`);";

$sql5 = "ALTER TABLE `az_survey_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$sql6 = "ALTER TABLE `az_survey_answers`
  ADD PRIMARY KEY (`id`);";
$sql7 = "ALTER TABLE `az_survey_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$sql8 = "ALTER TABLE `az_survey_forms`
  ADD PRIMARY KEY (`id`);";
$sql9 = "ALTER TABLE `az_survey_forms` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;";

	// Create the categories
$sql10 = "CREATE TABLE IF NOT EXISTS `az_survey_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`category_name` varchar(255) NOT NULL,
	`category_slug` varchar(255) NOT NULL,
	PRIMARY KEY(id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

// $sql11 = "ALTER TABLE `az_survey_categories`
//   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
// $sql12 = "ALTER TABLE `az_survey_categories`
//   ADD PRIMARY KEY (`id`);";

// create the result table
$sql11 = "CREATE TABLE IF NOT EXISTS `az_survey_activities` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`activity_name` varchar(255) NOT NULL,
	`activity_slug` varchar(255) NOT NULL,
	`activity_description` varchar(255) NOT NULL,
	`post_id` int(11),
	PRIMARY KEY (id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

// $sql14 = "ALTER TABLE `az_survey_activities`
// MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
// $sql15 = "ALTER TABLE `az_survey_activities`
// ADD PRIMARY KEY (`id`);";


	/** 
	* 0		0(Card Game) => 	0(recreation)	100(recreational)
	* 1		0(Card Game) =>		4(Individual)	50(both)
	*/
$sql12 = "CREATE TABLE IF NOT EXISTS `az_survey_activities_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`activity_id` int(11) NOT NULL,
	`category_id` int(11) NOT NULL,
	`rating` int(11) NOT NULL,
	PRIMARY KEY (id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";



	// Run the queries
	$result= $wpdb->get_results($sql1);
	$result= $wpdb->get_results($sql2);
	$result= $wpdb->get_results($sql3);
	$result= $wpdb->get_results($sql4);
	$result= $wpdb->get_results($sql5);
	$result= $wpdb->get_results($sql6);
	$result= $wpdb->get_results($sql7);
	$result= $wpdb->get_results($sql8);
	$result= $wpdb->get_results($sql9);
	$result= $wpdb->get_results($sql10);
	$result= $wpdb->get_results($sql11);
	$result= $wpdb->get_results($sql12);
	// $result= $wpdb->get_results($sql13);
	// $result= $wpdb->get_results($sql14);
	// $result= $wpdb->get_results($sql15);
	// $result= $wpdb->get_results($sql16);


	// Clear debug table entries
	$wpdb->query('TRUNCATE TABLE az_survey_categories');
	$wpdb->query('TRUNCATE TABLE az_survey_activities');
	$wpdb->query('TRUNCATE TABLE az_survey_activities_categories');


	 // Insert the categories
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Recreation', 'category_slug'=> 'recreation'));
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Outdoor', 'category_slug'=> 'outdoor'));
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Location', 'category_slug'=> 'location'));
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Intensity', 'category_slug'=> 'intensity'));
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Individual', 'category_slug'=> 'individual'));
	$wpdb->insert('az_survey_categories', array( 'category_name'=> 'Skills', 'category_slug'=> 'skills'));

	$wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Card Game', 'activity_slug'=> 'card_game', 'activity_description'=> 'recreation and indoor'));
	$wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Golf','activity_slug'=> 'golf', 'activity_description'=> 'Sport and outdoor'));
	$wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Cycling','activity_slug'=> 'cycling','activity_description'=> 'Sport and medium-high intensity'));
	$wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Mindfulness','activity_slug'=> 'mindfulness', 'activity_description'=> 'recreation, indoor and individual'));
	$wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Music', 'activity_slug'=> 'music','activity_description'=> 'Both, individual and recreation'));

	// Activity Card Games
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 1, 'rating'=> 100));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 2, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 3, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 4, 'rating'=> 0));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 5, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 6, 'rating'=> 50));

	// Activity music
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 1, 'rating'=> 100));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 2, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 3, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 4, 'rating'=> 0));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 5, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 6, 'rating'=> 0));

	// Activity mindfulness
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 1, 'rating'=> 100));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 2, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 3, 'rating'=> 50));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 4, 'rating'=> 0));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 5, 'rating'=> 100));
	$wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 6, 'rating'=> 0));
	
	echo $wpdb->last_error;

}

/**
 * Create the survey page shortcode
 */
 function az_surveyplus_func( $atts ){
 	ob_start();
 	$id = $atts['id'];
 	include('views/survey_form.php');
 	$content = ob_get_contents();
 	ob_end_clean();
		return $content;
}

// Register the shortcode
add_shortcode( 'az_surveyplus', 'az_surveyplus_func' );

/**
 * Create the result page shortcode
 */
function az_surveyplus_result_func( $atts ){

	wp_enqueue_style('main-styles', plugins_url() . '/Wordpress-Survey-Plugin/css/style.css');

	$id_list = [40,48,50];

	$args = array(
		//'p'         => 40, // ID of a page, post, or custom type
		'post__in' => $id_list,
		'orderby' => 'ASC',
		'post_type' => 'any'
	  );
	  $results = new WP_Query($args);

	//   die('<pre>'. print_r($results, true));

	/*


SELECT az_survey_activities.id as activity_id, activity_name, category_name, rating 
FROM az_survey_activities_categories
INNER JOIN az_survey_activities ON az_survey_activities.id = az_survey_activities_categories.activity_id
INNER JOIN az_survey_categories ON az_survey_categories.id = az_survey_activities_categories.category_id
WHERE 
	(category_name = 'Individual' AND rating >= '50')
OR (category_name = 'Skills' AND rating < '50')
ORDER BY activity_id
;	

	*/

	ob_start();
	$id = $atts['id'];
	include('views/survey_result.php');
	$content = ob_get_contents();
	ob_end_clean();
	   return $content;
}

// Register the shortcode
add_shortcode( 'az_surveyplus_result', 'az_surveyplus_result_func' );


function az_surveyplus_stats_func( $atts ){
 	ob_start();
 	$id = $atts['id'];
 	include('views/survey_form_stats.php');
 	$content = ob_get_contents();
 	ob_end_clean();
		return $content;
	}
	add_shortcode( 'az_surveyplus_stats', 'az_surveyplus_stats_func' );
function uninstall_process()
{
	global $wpdb; 

	$sql="DROP TABLE IF EXISTS az_survey_forms, az_survey_answers, az_survey_questions";

	$rows= $wpdb->get_results($sql);
	

}
register_activation_hook( __FILE__, 'activate_process' );
register_uninstall_hook( __FILE__, 'uninstall_process' );

	
function sfp_edit_survey_form()
{
	include 'admin/edit_survey_form.php';
}


function update_survey_form()
{
	//print_r($_POST); die;
	$title = $_POST['title'];
	$survey_form_id = $_POST['survey_form_id'];

	 global $wpdb; // this is required so that you can use wordpress to execute your sql queries

	 // inserting survey form
  	$result= $wpdb->update('az_survey_forms', array('title'=>$title), array('id'=>$survey_form_id));
  	echo $wpdb->last_error;
  
  		$arrQuestions = $_POST['questions'];
  		$arrQuestion_id = $_POST['question_id'];
		///print_r($_POST); die;
		if(!count($arrQuestions)){
			echo 1; wp_die();
			return false; }
		foreach($arrQuestions as $question_index => $question)
		{
			// inserting questions in database
			$question_id  = $arrQuestion_id[$question_index];
			$question_order = $_POST['question_order'][$question_index];
			if($question_id==0)
			{

  				$result= $wpdb->insert('az_survey_questions', array( 'question'=> $question, 'survey_id'=>$survey_form_id, 'orders'=>$question_order));
  				$question_id = $wpdb->insert_id;
			}
			else
			{

  				$result= $wpdb->update('az_survey_questions', array( 'question'=> $question, 'orders'=>$question_order), array('id'=>$question_id));
			}
			echo $wpdb->last_error;
  			
  				$arrAnswers = $_POST['answers'];
  				if($arrAnswers[$question_index])
  				{
  					//print_r($_POST['answer_id']); die;
					foreach($arrAnswers[$question_index] as $answer_index => $answer)
					{
						$answer_type = $_POST['answer_types'][$question_index][$answer_index];
						$answer_id = $_POST['answer_id'][$question_index][$answer_index];
						$answer_order = $_POST['answer_order'][$question_index][$answer_index]; 
						//echo $answer_id." ";
						if($answer_id==0)
						{
							//print_r($_POST['answer_order']); echo 'Question index: '.$question_index.' Answer Index: '.$answer_index;
							$result= $wpdb->insert('az_survey_answers', array('question_id'=>$question_id, 'answer'=> $answer, 'answer_type'=>$answer_type, 'orders'=>$answer_order));
						}
						else
						{
							
							$result= $wpdb->update('az_survey_answers', array( 'answer'=> $answer, 'answer_type'=>$answer_type, 'orders'=>$answer_order), array('id'=>$answer_id));
							
						}
	  					echo $wpdb->last_error;

					}
				}
		}
			echo 1;
			
	wp_die();
}
function delete_survey_process()
{
	global $wpdb;
	$survey_id = $_POST['survey_id'];
  	$result= $wpdb->delete('az_survey_forms',  array('id'=>$survey_id));
  	
  	if($result)
  	{
  		$q_ids = $wpdb->get_results('select id from az_survey_questions where survey_id='.$survey_id);
  		
  		if($q_ids)
  		{
  		
  			foreach($q_ids as $q_id)
	  		{
	  			$result= $wpdb->delete('az_survey_answers',  array('question_id'=>$q_id->id));
	  		}
  		}
  		$result= $wpdb->delete('az_survey_questions',  array('survey_id'=>$survey_id));

  		echo 1;
  	}
  	else
  	{
  		echo "Deletion Failed";
  	}
	wp_die();
}
add_action('wp_ajax_delete_survey_process', 'delete_survey_process');
add_action('wp_ajax_update_survey_form', 'update_survey_form');
add_action('wp_ajax_save_user_submission', 'save_user_submission');
add_action('wp_ajax_nopriv_save_user_submission', 'save_user_submission');

function save_user_submission()
{
	global $wpdb;
	$arr_question_ids = $_POST['question_ids'];
		//print_r($_POST); die;

	foreach($arr_question_ids as $question_index=>$question_id)
	{
		if($_POST['answers']['question_'.$question_index])
		{
			foreach($_POST['answers']['question_'.$question_index] as $answer_index => $answer)
			{
				

				if($answer_index==='open'){
					//echo 'inside open '.$answer_index; die;
					$answer_id = key($answer);
					if(empty($answer[$answer_id])){	continue; }
				}
				else
				{
					$answer_id = sanitize_text_field($answer);
				}

				// upvote this answer
				$result= $wpdb->query($wpdb->prepare("update az_survey_answers set votes = votes+1 where id='%d'",$answer_id));
				if(!$result)
				{
					echo $wpdb->last_error." id = $answer_id";;
					die;
				}
				//echo $wpdb->last_query;
			}
		}
		
	}

	echo 1;
	wp_die();
}


function ajax_delete_survey_answer()
{
	global $wpdb;
	if($_POST['answer_id'])
	{
		$id = sanitize_text_field($_POST['answer_id']);
		$result= $wpdb->delete('az_survey_answers',  array('id'=>$id));

		if($result)
		{
			echo json_encode(array('status'=>1, 'data'=>""));
		}
		else
		{
			echo json_encode(array('status'=>0, 'data'=>"Database Error.."));
		}
	}
	else
	{
		echo json_encode(array('status'=>0, 'data'=>"Answer Id is missing."));
	}
	
	wp_die();
}

add_action('wp_ajax_delete_survey_answer', 'ajax_delete_survey_answer');


function ajax_delete_survey_question()
{
	global $wpdb;
	if($_POST['question_id'])
	{
		$id = sanitize_text_field($_POST['question_id']);
		$result= $wpdb->delete('az_survey_questions',  array('id'=>$id));

		if($result)
		{
			// now removing answers
			$result= $wpdb->delete('az_survey_answers',  array('question_id'=>$id));
			
				echo json_encode(array('status'=>1, 'data'=>""));
			
			
		}
		else
		{
			echo json_encode(array('status'=>0, 'data'=>"Database Error.."));
		}
	}
	else
	{
		echo json_encode(array('status'=>0, 'data'=>"Question Id is missing."));
	}
	
	wp_die();
}

add_action('wp_ajax_delete_survey_question', 'ajax_delete_survey_question');

?>
