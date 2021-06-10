
<h3 style ="text-align:center;"><?php echo$error ??("Download Pdfs"); ?></h3>

<?php 
    if(!empty($directoryLinks)){
        foreach($directoryLinks as $name => $links){
                echo("Download: <a href=\"//".$links."\" targer=\"_blank\">".$name.".pdf"."</a><br>");
        }
    }else{
        echo("No Pdfs generated");
    }
    
    
?>