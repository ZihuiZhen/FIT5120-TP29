// Wait for the document to load
// document.addEventListener("DOMContentLoaded", () => {
// 	// Select the survey questions
// 	var $q4 = jQuery("#question-4");
// 	var $q5 = jQuery("#question-5");
// 	var $q6 = jQuery("#question-6");

// 	// Get each question's answers
// 	var $q4Answers = $q4.find(".answer");
// 	var $q5Answers = $q5.find(".answer");
// 	var $q6Answers = $q6.find(".answer");

// 	// Set category rating
// 	jQuery($q4Answers[0]).data("Sport", 100);
// 	jQuery($q4Answers[1]).data("Sport", 0);

// 	jQuery($q5Answers[0]).data("Indoors", 100);
// 	jQuery($q5Answers[1]).data("Indoors", 0);

// 	jQuery($q6Answers[0]).data("Intensity", 0);
// 	jQuery($q6Answers[1]).data("Intensity", 50);
// 	jQuery($q6Answers[2]).data("Intensity", 100);

// 	// Select the submit button
// 	var $submitBtn = jQuery("#submitSurvey");
// 	$submitBtn.click(function () {
// 		// alert("form submitted");
// 	});

// 	// Select the survey form
// 	var $surveyForm = jQuery("#survey_form");
// 	$surveyForm.submit(function (event) {
// 		console.log("Form Submitted");
// 		event.preventDefault();

// 		// Read form answers
// 		console.log(
// 			"Sport",
// 			jQuery($q4Answers[0]).find("input[type='radio']").val()
// 		);
// 		console.log(
// 			"Recreation",
// 			jQuery($q4Answers[1]).find("input[type='radio']").val()
// 		);

// 		console.log("form", $surveyForm.serialize());
// 	});
// });
