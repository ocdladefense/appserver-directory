<?php
	$occupationFieldsDefault = array("" => "All Occupation Fields");
	$allOccupationFields = $occupationFieldsDefault + $occupationFields;
	$selectedOccupationField = empty($selectedOccupation) ? "" : $selectedOccupation;

	$areasOfInterestDefault = array("" => "All Areas of Interest");
	$AllAreasOfInterest = $areasOfInterestDefault + $areasOfInterest;
	$selectedAreaOfInterest = empty($selectedInterest) ? "" : $selectedInterest;

	$includeExpertsCheck =  $includeExperts ? "1" : "0";
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
				<select name="Ocdla_Occupation_Field_Type__c">

					<?php foreach($allOccupationFields as $value => $label): ?>

						<?php $selected = $selectedOccupationField == $value ? "selected" : ""; ?>

						<option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>

					<?php endforeach; ?>
					
				</select>
			</div>
						
			<div class="form-item form-select">
				<select name="areaOfInterest">
					
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
				<button data-action="reset" class="button" type="button">Reset</button>
			</div>

			<!--
			<div class="form-item">
				<button data-action="update" data-form-element="IncludeExperts" class="button" id="control-experts" type="button">Experts</button>
			</div>
			-->

			<div class="form-item">
				<input data-action="submit" class="button" type="submit" value="Search" />
			</div>
		</div>

					
	</form>

</div>



<script type="application/javascript">


	let theform = document.getElementById("search-directory");
	theform.addEventListener("click",uxclick);
	/*
	// onchange="document.getElementById('search-directory').submit();
	const IncludeExperts = "<?php print $includeExpertsCheck; ?>";
	console.log(IncludeExperts);
	

	setValue("IncludeExperts", IncludeExperts);

	function toggleControl(elem) {
		if(elem.value == "1") {
			elem.classList.add("active");
		}
		else elem.classList.remove("active");
	}

	function setValue(name, value) {	
		theform.elements[name].value = value;

	}
	*/
	function uxclick(e) {
		let target = e.target;
		let name = target.name;
		let action = target.dataset && target.dataset.action;

		if(!["reset"].includes(action)) return;

		if("reset" == action) {
			window.location = "/directory/members";
			return;
		}
	}
</script>