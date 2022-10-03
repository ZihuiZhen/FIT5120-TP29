 <?php 
 global $wpdb; // this is required so that you can use wordpress to execute your sql queries
 
  $sql="SELECT *, (select count(az_survey_questions.id) from az_survey_questions where az_survey_questions.survey_id=az_survey_forms.id) as total_questions  FROM az_survey_forms";
  $rows= $wpdb->get_results($sql);
  echo $wpdb->last_error;
	?>
	<div class="wrap">
    <div id="success_msg" class="updated notice notice-success is-dismissible" style="display:none"><p>Survey Form is deleted successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>

        <h2><span class="dashicons dashicons-admin-comments"></span> Survey Forms</h2> 
        
           
		
        <form method="POST" action="">
            <table class="wp-list-table widefat fixed">
                <thead>
                  <tr>
                    <th scope="col" id="username" class="manage-column column-username" style="">
                       Title  
                    </th>
                    <th style="width:25%" scope="col" id="cb" class="manage-column column-cb " style="">
                      Total Questions
                    </th>
                    <th  style="width:25%">Short Code</th>
                
                    <th style="width:10%">
 
                    </th>
                    
                  </tr>
				        </thead>
              <tbody id="the-list">
                <?php
                foreach($rows as $survey)
                {
 
 
                ?>            
                <tr id="survey_<?php echo $survey->id;?>" data-id="<?php echo $survey->id;?>" class="<?php echo $survey->id;?>">
                  <td><?php echo $survey->title;?></td>
                  <td><?php echo $survey->total_questions; ?></td>
                  <td><code id="code_<?php echo $survey->id;?>" onclick="selectText('code_<?php echo $survey->id;?>')">[az_surveyplus id="<?php echo $survey->id;?>"]
                  </code><code id="code_stats_<?php echo $survey->id;?>"  onclick="selectText('code_stats_<?php echo $survey->id;?>')">
                    [az_surveyplus_stats id="<?php echo $survey->id;?>"]</code></td>
                  <td><a data-id="<?php echo $survey->id;?>" class="delete" href="javascript:void(0)">Delete</a> |  <a href="<?php echo  admin_url()."admin.php?page=edit-az-survey-form&action=edit&id=".$survey->id; ?>">Edit</a></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>		      
 <script type="text/javascript">
    $ = jQuery;
    function selectText(containerid) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
        }
    }
      

    $(".delete").click(function(e){
        var ajax_url    = '<?php echo admin_url('admin-ajax.php'); ?>';
      var survey_id = $(this).attr("data-id");
      var confirmation = window.confirm("Are you sure that you want to delete this?");
      if(confirmation)
      {
        $.ajax({
          data:{survey_id:survey_id, action:'delete_survey_process'},
          type:"POST",
          url:ajax_url

        }).done(function(responseTxt){
          if(responseTxt=='1')
          {
            $("#success_msg").slideDown(); 
            $("#survey_"+survey_id).fadeOut();
          }
          else
          {
            alert("Failed: "+responseTxt);
          }
        });
     
    }
  });
</script>