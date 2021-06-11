
<style>
.search-box label {
	font-weight:bold;
	display:block;
}

.search-box .form-item {
	display:block;
	margin-top:15px;
}

.search-box input,
.search-box select {
	display:block;
	font-size:1.5em;
	width:80%;
}

.search-title {
	font-size: 1.2em;
	font-weight:bold;
}

.search-box {
	margin-bottom:15px;
	line-height:1.4em;
	font-size:1.2em;
	width:49%;
}
</style>

<h1>OCDLA Online Membership Directory Search</h1>
<div class="content">
    <div class="node">
                <div id="search-page" style="margin-top:15px;">

            <div class="search-box">	
                <h2>Browse by Category</h2>
                <div class="search-item">
                    City
                </div>
                <div class="search-item">
                    County
                </div>
                <div class="search-item">
                    Areas of Interest
                </div>
                <div class="search-item">
                    Occupation/Field
                </div>
            </div>


            <div class="search-box">
                <h2>Membership Directory Section PDFs, Updated Daily</h2>
                <div class="search-item">
                    <?php 
                    if ($directoryLinks["CurrentMembersAlpha"]) {
                        print("<a href=\"".$directoryLinks['CurrentMembersAlpha']."\">Last Name</a>");
                    }
                    else{
                        print("Last Name");
                    }
                    ?>
                </div>
                <div class="search-item">
                <?php 
                    if ($directoryLinks["CurrentMembersCity"]) {
                        print("<a href=\"".$directoryLinks['CurrentMembersCity']."\">City</a>");
                    }
                    else{
                        print("City");
                    }
                    ?>
                </div>
                <div class="search-item">
                <?php 
                    if ($directoryLinks["CurrentMembersInterests"]) {
                        print("<a href=\"".$directoryLinks['CurrentMembersInterests']."\">Members Interests</a>");
                    }
                    else{
                        print("Members Interests");
                    }
                    ?>
                </div>
                <div class="search-item">
                <?php 
                    if ($directoryLinks["CurrentMembersNonlawyer"]) {
                        print("<a href=\"".$directoryLinks['CurrentMembersNonlawyer']."\">Nonlawyer Members</a>");
                    }
                    else{
                        print("Nonlawyer Members");
                    }
                    ?>
                </div>
                <div class="search-item">
                <?php 
                    if ($directoryLinks["CurrentPDContracts"]) {
                        print("<a href=\"".$directoryLinks['CurrentMembersPDContracts']."\">Public Defense Contracts</a>");
                    }
                    else{
                        print("Public Defense Contracts");
                    }
                    ?>
                    
                </div>
            </div>

            <div class="search-box">
                <a href="/sites/default/files/pdf/OCDLA_2021_Membership_Directory.pdf">
                    <img src="https://members.ocdla.org/sites/default/files/images/directory.jpg" style="width:60px;height:75px;float:right;border:none;">
                </a>
                <h2>Complete Membership Directory PDF</h2>
                <div class="search-item">
                    <a href="https://members.ocdla.org/sites/default/files/pdf/OCDLA_2021_Membership_Directory.pdf">Download</a>
                </div>
                <div class="search-item">
                    — also includes these sections, updated each September: <br>
                    • Public Defender Boards <br>
                    • Office of Public Defense Services Staff<br>
                    • Federal Public Defender Offices<br>
            </div>
            </div>

        </div>
    </div>
</div>




<script>console.log("Error: ".<?php echo($error); ?>);</script>
<?php if(empty($directoryLinks)): ?>
    <script>console.log("Message: ".<?php 
        $message = empty($directoryLinks) ? "" : "" ; 
        echo($message); ?>);
    </script>
<?php endif; ?>


<!-- <?php 
    if(!empty($directoryLinks)){
        foreach($directoryLinks as $name => $links){
                echo("Download: <a href=\"//".$links."\" targer=\"_blank\">".$name.".pdf"."</a><br>");
        }
    }else{
        echo("No Pdfs generated");
    }
    
    
?> -->