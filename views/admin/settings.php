<h1>Settings</h1>

<form action="options.php">
<?php 

	settings_fields('az_survey_options_group');
    do_settings_sections('az_survey_options_page');

?>

<input id="az_survey_result_page_url" type="submit" value="Submit">

</form>
