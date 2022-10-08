SELECT az_survey_activities.id as activity_id, activity_name, category_name, rating 
FROM az_survey_activities_categories
INNER JOIN az_survey_activities ON az_survey_activities.id = az_survey_activities_categories.activity_id
INNER JOIN az_survey_categories ON az_survey_categories.id = az_survey_activities_categories.category_id
WHERE 
	(category_name = 'Individual' AND rating >= '50')
OR (category_name = 'Skills' AND rating < '50')
ORDER BY activity_id
