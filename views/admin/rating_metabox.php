<?php

foreach ($catList as $category):

	$name = 'az_survey_rating_'. $category->slug;

?>

	<label for = '<?php echo $name ?>'><?php echo $category->name ?> </label><input type="text" id='<?php echo $name ?>' name='<?php echo $name ?>'>

<?php
endforeach;

