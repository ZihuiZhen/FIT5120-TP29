<h2>Statistics Survey</h2>
<style>
.progress .bar {
    background-color: rgb(91, 192, 222);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    filter: none !important;
    position: absolute;
    overflow: hidden;
    border-radius: 2px 2px 2px 2px;
    font-size: 11px;
    font-weight: bold;
    line-height: 13px;
    padding: 5px 0px 4px;
    width: 0%;
    height: 100%;
    color: rgb(255, 255, 255);
    float: left;
    text-align: center;
    text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.25);
    box-shadow: 0px -1px 0px rgba(0, 0, 0, 0.15) inset;
    -moz-box-sizing: border-box;
    transition: width 1.6s ease 2s;
    -webkit-transition: width 1.6s ease 2s;
}

.progress {
    filter: none !important;
    position: relative;
    overflow: hidden;
    height: 22px;
    background: none repeat scroll 0% 0% rgb(239, 239, 239);
    box-shadow: none;
    border-radius: 2px 2px 2px 2px;
    border: 1px solid rgb(213, 213, 213);
    margin-bottom:40px;
}

h5 
{
	margin:0px !important; font-weight:400 !important;
}
</style>
<?php 
	global $wpdb; 
	$sql="select * from az_survey_questions where survey_id=".$id;
	$rows= $wpdb->get_results($sql);

	foreach($rows as $row)
	{
			 ?>
<h3>Q: <?php echo stripslashes($row->question); ?><h3>
<?php 
$sql="select *, (select sum(votes) from az_survey_answers where question_id=".$row->id.") as total_voters  from az_survey_answers where question_id=".$row->id;
	$answers= $wpdb->get_results($sql);

	foreach($answers as $answer)
	{

		$percentage = ($answer->total_voters!=0)?round(100 * $answer->votes / $answer->total_voters):0;
		?>
<h5 style="float:left;">Ans: <?php echo stripslashes($answer->answer); ?></h5> 
<div style="float:right"><?php echo $answer->votes."/".$answer->total_voters; ?></div>
<div style="clear:both"></div>
<div class="progress progress-info progress-striped">
	<div class="bar survey_global_percent" style="width: <?php echo $percentage; ?>%; "><?php echo $percentage; ?>%</div>
</div>

<?php 
	}

} ?>