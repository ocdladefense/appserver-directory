<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />

<style type="text/css">
@media screen and (min-width: 800px) {
#stage.column.column-middle {
    width: 92%;
}
#container-right {
	display: none;
}
}
</style>
<script type="text/javascript">
	const mapKey = "<?php print GOOGLE_MAP_KEY; ?>";
</script>
<script src="<?php print module_path(); ?>/dist/OCDLATheme.js"></script>
<script src="<?php print module_path(); ?>/dist/Member.js"></script>
<script type="module" src="<?php print module_path(); ?>/src/js/app.js"></script>


<script type="application/javascript">
	const query = '<?php echo $conditions; ?>';
</script>



<div>
	<h2>OCDLA Member Directory</h2>
</div>

<div id="search-widget">
	<?php print $search;  ?>
</div>

<?php if(is_admin_user()): ?>

	<div>
		<strong><?php print $query; ?></strong>
	</div>

<?php endif; ?>



<?php if(!isset($contacts) || (isset($contacts) && count($contacts) < 1)): ?>
	<ul class="table-row">
		<li>There are no members that meet your search criteria.</li>
	</ul>
<?php endif; ?>


<button onclick="switchView('map')">Map View</button>
<button onclick="switchView('list')">List View</button>



<!-- NOTE: COUNT WILL HAVE TO BE RECALCULATED -->
<div>
	<p><?php print "Showing $count members"; ?></p>
</div>

<div id="view" class="view table view-table">


	<ul class="table-row">
		<li class="table-header">Name</li>
		<li class="table-header">Field</li>
		<li class="table-header">Organization</li>
		<li class="table-header">Phone</li>
		<li class="table-header">City</li>
		<li class="table-header">Email</li>
		<li class="table-header">Areas of Interest</li>
	</ul>

		


	<?php foreach($contacts as $contact): ?>
		<?php $areasOfInterest = $contact->getAreasOfInterest(); ?>

		<ul class="table-row"> 

			<li class="table-cell">
				<a href="/directory/member/<?php print $contact->getId(); ?>">
					<?php print $contact->FirstName . " " . $contact->LastName; ?>
				</a>
			</li>
			<li class="table-cell">
				<?php print $contact->Ocdla_Occupation_Field_Type__c; ?>
			</li>
			<li class="table-cell">
				<?php print $contact->Ocdla_Organization__c; ?>
			</li>
			<li class="table-cell short-cell">
				<a href="tel:<?php print $contact->getPhoneNumericOnly(); ?>">
					<?php print $contact->Phone; ?>
				</a>
			</li>
			<li class="table-cell short-cell">
				<?php print $contact->MailingCity; ?>
			</li>
			<li class="table-cell">
				<a href="mailto:<?php print $contact->Email; ?>">
					<?php print $contact->Email; ?>
				</a>
			</li>
			<li class="table-cell long-cell">
				<?php print $areasOfInterest; ?>
			</li>
			
		</ul>
	<?php endforeach; ?>


</div>