<?php
	$occupationFieldsDefault = array("" => "All Occupation Fields");
	$allOccupationFields = $occupationFieldsDefault + $occupationFields;
	$selectedOccupationField = empty($selectedOccupation) ? "" : $selectedOccupation;

	$areasOfInterestDefault = array("" => "All Areas of Interest");
	$AllAreasOfInterest = $areasOfInterestDefault + $areasOfInterest;
	$selectedAreaOfInterest = empty($selectedInterest) ? "" : $selectedInterest;

	$includeExpertsCheck =  $includeExperts ? "checked" : "";
?>

<div class="search-container">

	<form id="search-directory" action="/directory/members" method="post">


		<div class="form-group">
			<div class="form-item">
				<input name="FirstName" value="<?php print $firstName; ?>" size="20" maxlength="35" type="text" placeholder="First Name" />
			</div>
				
			<div class="form-item">
				<input name="LastName" value="<?php print $lastName; ?>" size="20" maxlength="35" type="text" placeholder="Last Name" />
			</div>
			
			<div class="form-item">
				<input name="Ocdla_Organization__c" value="<?php print $companyName; ?>" size="20" maxlength="35" type="text" placeholder="Company Name" /> 
			</div>
				
			<div class="form-item">
				<input name="MailingCity" size="20" value="<?php print $city; ?>" maxlength="35" type="text" placeholder="City" />
			</div>
		</div>

		<div class="form-group">
			<div class="form-item form-select">
				<select name="Ocdla_Occupation_Field_Type__c" onchange="document.getElementById('search-directory').submit()">

					<?php foreach($allOccupationFields as $value => $label): ?>

						<?php $selected = $selectedOccupationField == $value ? "selected" : ""; ?>

						<option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>

					<?php endforeach; ?>
					
				</select>
			</div>
						
			<div class="form-item form-select">
				<select name="areaOfInterest" onchange="document.getElementById('search-directory').submit()">
					
					<?php foreach($AllAreasOfInterest as $value => $label): ?>

						<?php $selected = $selectedAreaOfInterest == $value ? "selected" : ""; ?>

						<option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>

					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="form-group">

			<div class="form-item">
				<input name="IncludeExperts" type="hidden" value="0" />
				<button class="button" type="button">Clear</button>
			</div>

			<div class="form-item">
				<button class="button" id="control-experts" type="button">Experts</button>
			</div>

			<div class="form-item">
				<button class="button" type="submit">Search</button>
			</div>
		</div>

					
	</form>

</div>



<script type="application/javascript">
// onchange="document.getElementById('search-directory').submit();
const includeExperts = "<?php print $includeExperts; ?>";
console.log(includeExperts);
setValue("includeExperts", includeExperts);

function toggleControl(elem) {
    if(elem.value == "1") {
        elem.classList.add("active");
    }
    else elem.classList.remove("active");
}

function setValue(name, value) {
	let theform = document.getElementsById("search");
	theForm.elements[name].value = value;

}


function uxclick(e) {
	let target = e.target;
	let name = target.name;
	if(["includeExperts"].includes(name)) {
		
	}
}

function submitForm() {
	let theform = document.getElementsById("search");
	theForm.submit();
}
</script>