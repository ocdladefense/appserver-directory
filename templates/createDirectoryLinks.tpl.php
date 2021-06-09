<h3 style ="text-align:center;">Generate Pdfs</h3>

<?php 
    foreach($cloudConvertLinks as $name => $links){
        echo("<a href=\"//".$links."\">"."Generate ".$name." directories"."</a><br>");
    }
?>