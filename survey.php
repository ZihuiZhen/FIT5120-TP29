<?php 
    /*
    Plugin Name: Survey Plus
    Description: Plugin for showing and creating survey form
    Author: Ayyaz Zafar
    Version: 1.10.1
    Author URI: http://www.AyyazZafar.com


    */


if (!session_id()) {
    session_start();
}


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
	add_submenu_page ('survey-plus',  'Settings', 'Settings', 1, 'az-survey-settings', "spf_settings");


}

add_action( 'admin_init' , 'az_survey_admin_init' );

function az_survey_admin_init(){
	// Register a new setting
	register_setting( 'az_survey_options_group', 'az_survey_results_page_url' );

	// Register a new section in the "wporg" page.
	add_settings_section(
		'az_survey_general_options',
		__( 'General settings', 'az_survey' ), 
		'az_survey_general_settings',
		'az-survey-settings'
	);

	// Register a new field in the "wporg_section_developers" section, inside the "wporg" page.
	add_settings_field(
		'az_survey_results_page_url', // As of WP 4.6 this value is used only internally.
								// Use $args' label_for to populate the id inside the callback.
		__( 'Result Page URL', 'az_survey' ),
		'az_survey_results_page_url',
		'az-survey-settings',
		'az_survey_general_options',
);
}
	
function spf_settings()
{


	include("views/admin/settings.php");
}

function az_survey_general_settings()
{
    // echo    "general Settings";
}

function az_survey_results_page_url()
{
	$results_page_url = get_option('az_survey_results_page_url');

	include("views/fields/results_page_url.php");
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
$sql12 = "CREATE TABLE IF NOT EXISTS `az_survey_answer_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`form_id` int(11) NOT NULL,
	`answer_id` int(11) NOT NULL,
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
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Recreation', 'category_slug'=> 'recreation'));
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Outdoor', 'category_slug'=> 'outdoor'));
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Location', 'category_slug'=> 'location'));
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Intensity', 'category_slug'=> 'intensity'));
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Individual', 'category_slug'=> 'individual'));
	// $wpdb->insert('az_survey_categories', array( 'category_name'=> 'Skills', 'category_slug'=> 'skills'));

	// $wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Card Game', 'activity_slug'=> 'card_game', 'activity_description'=> 'recreation and indoor'));
	// $wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Golf','activity_slug'=> 'golf', 'activity_description'=> 'Sport and outdoor'));
	// $wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Cycling','activity_slug'=> 'cycling','activity_description'=> 'Sport and medium-high intensity'));
	// $wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Mindfulness','activity_slug'=> 'mindfulness', 'activity_description'=> 'recreation, indoor and individual'));
	// $wpdb->insert('az_survey_activities', array( 'activity_name'=> 'Music', 'activity_slug'=> 'music','activity_description'=> 'Both, individual and recreation'));

	// // Activity Card Games
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 1, 'rating'=> 100));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 2, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 3, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 4, 'rating'=> 0));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 5, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 1, 'category_id'=> 6, 'rating'=> 50));

	// // Activity music
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 1, 'rating'=> 100));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 2, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 3, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 4, 'rating'=> 0));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 5, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 5, 'category_id'=> 6, 'rating'=> 0));

	// // Activity mindfulness
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 1, 'rating'=> 100));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 2, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 3, 'rating'=> 50));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 4, 'rating'=> 0));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 5, 'rating'=> 100));
	// $wpdb->insert('az_survey_activities_categories', array( 'activity_id'=> 4, 'category_id'=> 6, 'rating'=> 0));
	
	echo $wpdb->last_error;

}

/**
 * Create the survey page shortcode
 */
 function az_surveyplus_func( $atts ){

	$_SESSION['az_survey']['survey_submit'] = null;
	$results_page_url = get_option('az_survey_results_page_url');

 	ob_start();
 	$id = $atts['id'];
 	include('views/survey_form.php');
 	$content = ob_get_contents();
 	ob_end_clean();
		return $content;
}

// Register the shortcode
add_shortcode( 'az_surveyplus', 'az_surveyplus_func' );

function az_survey_get_meta_options($meta_key, $meta_value, &$allpostMeta, &$id_list){
	
	global $wpdb;

	if(!empty($meta_key)){

		$sql = "SELECT * ".
			"FROM wp_postmeta ".  
			"WHERE meta_key=%s ".
			"AND meta_value=%s ";

		$meta_options = $wpdb->get_results($wpdb->prepare($sql, $meta_key, $meta_value));
		// echo 'meta_options:<pre>'. print_r($meta_options, true).  '</pre>';

		foreach($meta_options as $meta_option){
			
			$meta = array(
				'post_id' => $meta_option->post_id,
				'meta_key'  => $meta_option->meta_key,
				'meta_value' => $meta_option->meta_value
				
			);

			if (empty($allpostMeta['post_id_'.$meta_option->post_id])) {

				$allpostMeta['post_id_'.$meta_option->post_id] = [];
			}

			if (!in_array($meta_option->post_id, $id_list)) {

				array_push($id_list, $meta_option->post_id);
			}
			
			array_push($allpostMeta['post_id_'.$meta_option->post_id], $meta);
			

		}
	}
}

function az_survey_results_sort_order($record1, $record2)
{
	// sort by the lowest count in reverse order
	if($record1['count'] < $record2['count']) {

		return 1;
	}

	// sort by the highest count in reverse order
	if($record1['count'] > $record2['count']) {

		return -1;
	}

	// sort by alphabetical order
	return strcasecmp($record1['title'], $record2['title']);
}

/**
 * Create the results page shortcode
 */
function az_surveyplus_results_func( $atts ){

	global $wpdb;
	wp_enqueue_style('main-styles', plugins_url() . '/Wordpress-Survey-Plugin/css/style.css');

	if(!empty($_SESSION['az_survey']) && !empty($_SESSION['az_survey']['survey_submit'])){

		$arr_question_ids = $_SESSION['az_survey']['survey_submit']['question_ids'];
		$arr_answers = $_SESSION['az_survey']['survey_submit']['answers'];

		$allpostMeta = [];
		$id_list = [];
		foreach($arr_answers as $key=>$answer){

			$answer_id = $answer[0];
			$records = $wpdb->get_results($wpdb->prepare("select * from az_survey_answer_categories where answer_id=%s",$answer_id));
			// echo 'records:<pre>'. print_r($records, true).  '</pre>';


			// Find the answer categories
			/* disable finding categories and post meta
			if(!empty($records)){

				$category = get_category($records[0]->category_id);
				// echo "cat id:". $records[0]->category_id. "<br>";
				// echo 'category:<pre>'. print_r($category,true). '</pre>';

				$meta_key = 'az_survey_rating_'.$category->slug;
				$meta_value = $records[0]->rating;
				// echo $meta_key. "<br>";

			}

			az_survey_get_meta_options($meta_key, $meta_value, $allpostMeta, $id_list);
			*/

			if(!empty($records)) {
				$args = array(
					// find activity posts
					'post_type' => 'az_survey_activity',

					// find for category id
					'category' => $records[0]->category_id,

					// // no limit for number of posts
					'numberposts' => -1,
				);
				$posts = get_posts($args);
				// echo 'posts<pre>'. print_r($posts, true). '</pre>';

				if(!empty($posts)) {

					foreach($posts as $post) {

						$key = 'post_id_'. $post->ID;
						// create a value for the first time
						if (empty($allpostMeta[$key])) {

							$allpostMeta[$key] = array(
								'post_id' => $post->ID,
								'title' => $post->post_title,
								'count' => 1,
							);

						// update the value
						} else {

							$allpostMeta[$key]['count'] = $allpostMeta[$key]['count'] + 1;
						}
					}
				}
			}
		}

		usort($allpostMeta, 'az_survey_results_sort_order');

		// echo 'allpostMeta:<pre>'. print_r($allpostMeta, true).  '</pre>';
		foreach($allpostMeta as $post) {
			array_push($id_list, $post['post_id']);
		}
		// echo 'id_list:<pre>'. print_r($id_list, true).  '</pre>';
	}


	// $id_list = [40,48,50];

	$args = array(
		//'p'         => 40, // ID of a page, post, or custom type
		'post__in' => $id_list,
		'orderby' => 'post__in',
		'post_type' => 'az_survey_activity',
		'number' => 3
	  );
	  $results = new WP_Query($args);

	ob_start();
	$id = $atts['id'];
	include('views/survey_results.php');
	$content = ob_get_contents();
	ob_end_clean();
	   return $content;
}

// Register the shortcode
add_shortcode( 'az_surveyplus_results', 'az_surveyplus_results_func' );


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
	global $wpdb;

	$args = array(
		// get all the categories
		'hide_empty' =>  false,
	);
	$categories = get_categories($args);

	$form_id = $_GET['id'];
	$records = $wpdb->get_results($wpdb->prepare("select * from az_survey_answer_categories where form_id=%s",$form_id));

	$answer_ratings = [];
	foreach($records as $record){
		$answer_ratings[$record->answer_id] = $record;
	}

	// echo 'answer 5 = <pre>'. print_r($answer_ratings, true). '</pre>';

	// echo '<pre>'.print_r($records,true).'</pre>';


	include 'admin/edit_survey_form.php';
}

function update_survey_form()
{
	// print_r($_POST); die;
	//echo json_encode($_POST); die;
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

		$response = az_survey_update_answer_category_rating();

	echo json_encode($response);
			
	wp_die();
}

function az_survey_update_answer_category_rating()
{
	global $wpdb;

	$arrQuestions = $_POST['questions'];
	$arrQuestion_id = $_POST['question_id'];

	$recordList = [];

	// Loop through the questions
	foreach ($arrQuestions as $question_index => $question) {

		// Loop through the answers
		$arrAnswer_id = $_POST['answer_id'][$question_index];
		foreach ($arrAnswer_id as $answer_index => $answer){
			
			$record = [
				'id' => intval($_POST['ac_record_id'][$question_index][$answer_index]),
				'form_id' => intval($_POST['survey_form_id']),
				'answer_id' => intval($_POST['answer_id'][$question_index][$answer_index]),
				'category_id' => intval($_POST['answer_category_id'][$question_index][$answer_index]),
				'rating' => intval($_POST['answer_category_rating'][$question_index][$answer_index]),
			];

			array_push($recordList, $record);
		}	
	}

	// Insert records
	foreach ($recordList as $record){
		


		if($record['id']==0)
		{
			$wpdb->insert('az_survey_answer_categories', $record);

		}
		else
		{
			$wpdb->update('az_survey_answer_categories', $record, array('id' => $record['id']));
		}
	}
		

	return $recordList;

	// $recordList = [
	// 	[
	// 		'qid' => 0,
	// 		'answer_cnt' => 2,
	// 		'question' => 'some text',
	// 	],
	// 	[
	// 		'qid' => 0,
	// 		'answer_cnt' => 2,
	// 		'question' => 'some text',
	// 	],
	// 	[
	// 		'qid' => 0,
	// 		'answer_cnt' => 2,
	// 		'question' => 'some text',
	// 	],
	// ]

	// id, answer_id, category_id, rating
	// $record = [
	// 	'id' => 0,
	// 	'answer_id' => $_POST['answer_id'][0][0],
	// 	'category_id' => $_POST['answer_category_id'][0][0],
	// 	'rating' => $_POST['answer_category_rating'][0][0],
	// ];
	
	return $record;
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
	// print_r($_POST); die;

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

	$response = az_survey_user_submission_session();


	wp_send_json_success($response);
	// echo json_encode($score);
	// wp_die();
}

function az_survey_user_submission_session(){

	$_SESSION['az_survey'] = array(

		'survey_submit' =>array(
			
			'question_ids' => $_POST['question_ids'],
			'answers' => $_POST['answers']

		)
	);

	return true;
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


// Our custom post type function
function az_survey_create_posttype() {
  
    register_post_type( 'az_survey_activity',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Activities' ),
                'singular_name' => __( 'Activity' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'az_survey_activity'),
            'show_in_rest' => true,
				
			'taxonomies'  => array( 'category', 'tags' ,'tag'),

			'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', ),

			// 'show_in_menu' => 'survey-plus',
  
        )
    );
}

function az_survey_addmetaboxes() {
	add_meta_box('az_survey_rating', 'Rating', 'az_survey_rating_MB', 'az_survey_activity', 'side');
}


function az_survey_init() {
	az_survey_create_posttype();
}

function az_survey_filter_field_names($field_list, $field_name){
	
	$az_fields = [];

	foreach ($field_list as $key => $value){

		if (str_contains($key, $field_name)) {
			// echo $key.' : '.$value[0].'<br/>';
			$az_fields = array_merge($az_fields, array($key => $value[0]));
		}
	}

	return $az_fields;
}

function az_survey_rating_MB( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'az_survey_rating_nonce', 'az_survey_rating_nonce' );

	
    $all_meta = get_post_meta( $post->ID);
	$az_meta = az_survey_filter_field_names($all_meta,'az_survey_rating');

	$catList = az_survey_get_post_categories($post->ID);

	include("views/admin/rating_metabox.php");
}

function az_survey_get_post_categories($post_id){

	$postCatIDs = wp_get_post_categories($post_id);
	$catList = get_categories(array(
		'include' => $postCatIDs
	));
	
	return $catList;
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id
 */
function az_survey_rating_meta_box_save_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['az_survey_rating_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['az_survey_rating_nonce'], 'az_survey_rating_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

	
	$all_meta = get_post_meta( $post_id);
	$az_meta = az_survey_filter_field_names($all_meta,'az_survey_rating');
	
	$catRatings = array();

	$catList = az_survey_get_post_categories($post_id);
	foreach ($catList as $category) {

		$name = 'az_survey_rating_'. $category->slug;

		// Make sure that it is set.
		if (isset( $_POST[$name] ) ) {
			$catRatings[$name] = intval($_POST[$name]);
			update_post_meta( $post_id, $name, $catRatings[$name] );
		}
	}

	// $az_meta = [
	// 	'az_survey_ratings' => 'asdfasdf',
	// 	'az_survey_skill' => 50,
	// 	'az_survey_location' => 50
	// ];

	// $catRatings = [
	// 	'az_survey_skill' => 70,
	// 	'az_survey_indoor' => 100,
	// ];

	$remove_fields = array_diff_key($az_meta,$catRatings);

	// $remove_fields = [
	// 	'az_survey_ratings' => 'asdfasdf',
	// 	'az_survey_location' => 50,
	// ];


	foreach ($remove_fields as $key => $value){
		delete_post_meta($post_id, $key);
	}

    // Sanitize user input.
    // $my_data = sanitize_text_field( $_POST['az_survey_rating'] );

	

    // Update the meta field in the database.
    // update_post_meta( $post_id, 'az_survey_rating', $my_data );
}



// Hooking up our function to theme setup
add_action( 'init', 'az_survey_init' );

// Disable meta box and post save
//add_action('add_meta_boxes', 'az_survey_addmetaboxes');
//add_action( 'save_post', 'az_survey_rating_meta_box_save_data' );

add_action('wp_enqueue_scripts', 'az_survey_enqueue_scripts');

function az_survey_enqueue_scripts()
{

	wp_enqueue_script( 'jquery-script', 'https://code.jquery.com/jquery-2.2.4.min.js');
	wp_enqueue_style('main-styles', plugins_url() . '/Wordpress-Survey-Plugin/css/style.css');
	//wp_enqueue_script( 'az_survey_script', plugins_url(). '/Wordpress-Survey-Plugin/js/survey.js', false );	
}