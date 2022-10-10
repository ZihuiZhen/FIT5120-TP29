
<div class="wrap">

<?php
if ( $results->have_posts() ) :
    while ( $results->have_posts() ) :
		 
		$results->the_post();
		
?>
	<div id="activity-<?php the_ID() ?>" class="az_survey_activity">
	
		<div class="title-wrap">
			<h2><?php the_title() ?></h2> <div class = "activity-read-more"><?php the_shortlink('read more >>>>') ?></div>
		</div>
		<div class="activity-content">

		<?php
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			the_post_thumbnail();
			}
		?>

			<!-- <img src="https://tse1.mm.bing.net/th?id=OIP.7YilmViNQMnwjr6dgBNPvQHaFS&pid=Api&P=0" alt="Image"> -->

			<div class="description"><?php the_excerpt() ?></div>
		</div>
	</div>	

<?php
        // Your loop code
    endwhile;
else :
    _e( 'Sorry, no posts were found.', 'textdomain' );
endif;
?>


</div>


