<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory-search.css" />

<?php
	array_unshift($occupationFields, "All Occupations/Fields");
	array_unshift($areasOfInterest, "All Areas of Interest");

	$selectedOccupationField = empty($selectedOccupation) ? "All Fields" : $selectedOccupation;
	$selectedAreaOfInterest = empty($selectedInterest) ? "All Interests" : $selectedInterest;

?>



<div class="search-bar">

	<form id="search-directory" action="/directory/search" method="post">

		<Strong>Search</strong>
		<?php print "showing $count people."; ?>

		<div class="form-item checkbox">
			Include Expert Witnesses:
			<input name="IncludeExperts" value="1" checked="checked" type="checkbox">
		</div>

		<br />

		<div class="form-item form-select">
			<select name="Ocdla_Occupation_Field_Type__c" onchange="document.getElementById('search-directory').submit()">

				<?php
					foreach ($occupationFields as $field){

						$selected = $selectedOccupationField == $field ? "selected" : "";
					?>

					<option value="<?php print $field; ?>" <?php print $selected; ?>><?php print $field; ?></option>

				<?php } ?>
				
			</select>
		</div>
					
		<div class="form-item form-select">
			<select name="areaOfInterest" onchange="document.getElementById('search-directory').submit()">
				<?php
						foreach ($areasOfInterest as $area){

							$selected = $selectedAreaOfInterest == $area ? "selected" : "";
						?>

						<option value="<?php print $area; ?>" <?php print $selected; ?>><?php print $area; ?></option>

					<?php } ?>
			</select>
		</div>

		<div class="form-item">
			<input name="FirstName" size="20" value="" maxlength="35" type="text" placeholder="First Name">
		</div>
			
		<div class="form-item">
			<input name="LastName" size="20" value="" maxlength="35" type="text" placeholder="Last Name">
		</div>
		
		<div class="form-item">
			<input name="Ocdla_Organization__c" size="20" value="" maxlength="35" type="text" placeholder="Company Name"> 
		</div>
			
		<div class="form-item">
			<input name="MailingCity" size="20" value="" maxlength="35" type="text" placeholder="City">
		</div>

		<br />

		<div class="form-item">
			<button type="submit">SEARCH</button>
		</div>

	</form>

</div>