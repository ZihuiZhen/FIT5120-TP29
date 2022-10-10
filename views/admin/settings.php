<h1>Settings</h1>

<form method='post' action="options.php">
<?php 

	settings_fields('az_survey_options_group');
    do_settings_sections('az-survey-settings');
	submit_button();
?>

</form>
