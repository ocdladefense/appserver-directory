<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory-search.css" />

<?php
	array_unshift($occupationFields, "All Occupations/Fields");
	array_unshift($areasOfInterest, "All Areas of Interest");

	$selectedOccupationField = empty($selectedOccupation) ? "All Fields" : $selectedOccupation;
	$selectedAreaOfInterest = empty($selectedInterest) ? "All Interests" : $selectedInterest;

	$includeExpertsCheck = $includeExperts ? "checked" : "";
?>



<div class="container">

	<form id="search-directory" action="/directory/search" method="post">

		<div class="search-row header-row">

			<div class="form-item first-item">
				<Strong>Search</strong>
			</div>

			<div class="form-item">
				<?php print "showing $count people."; ?>
			</div>

			<div class="form-item">
				<button type="submit">SUBMIT SEARCH</button>
			</div>

			<div class="form-item">
				<a href="/directory/search" style="text-decoration:none;">CLEAR SEARCH</a>
			</div>


			<div class="form-item last-item">
				Include Expert Witnesses:
				<input name="IncludeExperts" value="1" <?php print $includeExpertsCheck; ?> type="checkbox" onchange="document.getElementById('search-directory').submit()">
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
				<input name="Ocdla_Organization__c" value="<?php print $companyName; ?>" size="20" maxlength="35" type="text" placeholder="Company Name"> 
			</div>
				
			<div class="form-item">
				<input name="MailingCity" size="20" value="<?php print $city; ?>" maxlength="35" type="text" placeholder="City">
			</div>

		</div> <!--end search row -->

		<div class="search-row">

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

		</div> <!--end search row -->

	</form>

</div>

<script>

	var timer;              
	var doneTypingInterval = 1000;
	var $input = $('input');

	$input.on('keyup', function () {
	clearTimeout(timer);
	timer = setTimeout(doneTyping, doneTypingInterval);
	});

	$input.on('keydown', function () {
	clearTimeout(timer);
	});

	function doneTyping () {
	
		document.getElementById("search-directory").submit();
	}

</script>