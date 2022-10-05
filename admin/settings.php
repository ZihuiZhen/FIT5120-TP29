<?php 

// Register a new setting
register_setting( 'options', 'az_survey_options' );

// Register a new section in the "wporg" page.
add_settings_section(
    'az_survey_options',
    __( 'General settings', 'az_survey' ), 'az_survey_general_settings',
    'az_survey_options_page'
);

// Register a new field in the "wporg_section_developers" section, inside the "wporg" page.
add_settings_field(
    'az_survey_result_page_url', // As of WP 4.6 this value is used only internally.
                            // Use $args' label_for to populate the id inside the callback.
        __( 'Result Page URL', 'az_survey' ),
    'az_survey_result_page_url',
    'az_survey',
    'az_survey_options',
    array(
        'label_for'         => 'az_survey_result_page_url',
        // 'class'             => 'wporg_row',
        // 'wporg_custom_data' => 'custom',
    )
);

function az_survey_general_settings()
{
    echo    "general Settings";
}

function az_survey_result_page_url()
{
    echo    "result page";
}
