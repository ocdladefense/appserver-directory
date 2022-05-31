

<?php
	$default = array("" => "All Primary Fields");
	$all = $default + $primaryFields;

	$selectedPrimaryField = empty($selectedPrimaryField) ? "" : $selectedPrimaryField;
?>

<div class="container">

	<form id="search-directory" action="/directory/experts" method="post">

		<div class="search-row header-row">

			<div class="form-item first-item">
				<a href="/directory/members" style="text-decoration:none;">Go to member directory</a>
			</div>

			<div class="form-item">
				<a href="/directory/experts" style="text-decoration:none;">CLEAR SEARCH</a>
			</div>

			<div class="form-item">
				<button type="submit">SUBMIT SEARCH</button>
			</div>

		</div> <!--end search row -->

		<div class="search-row">
		
			<div class="form-item">
				<input name="FirstName" value="<?php print $firstName; ?>" size="20" maxlength="35" type="text" placeholder="First Name">
			</div>
				
			<div class="form-item">
				<input name="LastName" value="<?php print $lastName; ?>" size="20" maxlength="35" type="text" placeholder="Last Name">
			</div>
			
			<div class="form-item">
				<input name="Ocdla_Organization__c" value="<?php print $companyName; ?>" size="20" maxlength="35" type="text" placeholder="Organization Name"> 
			</div>
				
			<div class="form-item">
				<input name="MailingCity" size="20" value="<?php print $city; ?>" maxlength="35" type="text" placeholder="City">
			</div>

			<div class="form-item form-select">
				<select name="Ocdla_Expert_Witness_Primary__c" onchange="document.getElementById('search-directory').submit()">

					<?php
						foreach ($all as $value => $label){

							$selected = $selectedPrimaryField == $value ? "selected" : "";
						?>

						<option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>

					<?php } ?>
					
				</select>
			</div>

		</div> <!--end search row -->

		<div class="search-row optional-row">
		</div> <!--end search row -->

	</form>

</div>