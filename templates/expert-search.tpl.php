<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />

<?php
	array_unshift($primaryFields, "All Primary Fields");

	$selectedPrimaryField = empty($selectedPrimaryField) ? "All Primary Fields" : $selectedPrimaryField;
?>

<div class="container">

	<form id="search-directory" action="/directory/experts" method="post">

		<div class="search-row header-row">

			<div class="form-item first-item">
				<Strong>Search the directory</strong>
			</div>

			<div class="form-item">
				<button type="submit">SUBMIT SEARCH</button>
			</div>

			<div class="form-item">
				<a href="/directory/experts" style="text-decoration:none;">CLEAR SEARCH</a>
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

			<div class="form-item form-select">
				<select name="Ocdla_Occupation_Field_Type__c" onchange="document.getElementById('search-directory').submit()">

					<?php
						foreach ($primaryFields as $field){

							$selected = $selectedPrimaryField == $field ? "selected" : "";
						?>

						<option value="<?php print $field; ?>" <?php print $selected; ?>><?php print $field; ?></option>

					<?php } ?>
					
				</select>
			</div>

		</div> <!--end search row -->

		<div class="search-row optional-row">
		</div> <!--end search row -->

	</form>

</div>