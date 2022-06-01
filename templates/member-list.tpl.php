<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />


<script src="<?php print module_path(); ?>/dist/mapkey.js"></script>
<script src="<?php print module_path(); ?>/dist/OCDLATheme.js"></script>
<script src="<?php print module_path(); ?>/dist/Member.js"></script>
<script type="module" src="<?php print module_path(); ?>/dist/views.js"></script>


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
				<a href="/directory/members/<?php print $contact->getId(); ?>"><?php print $contact->getName(); ?></a>
			</li>
			<li class="table-cell"><?php print $contact->getOccupationFieldType(); ?></li>
			<li class="table-cell"><?php print $contact->getOcdlaOrganization(); ?></li>
			<li class="table-cell short-cell">
				<a href="tel:<?php print $contact->getPhoneNumericOnly(); ?>"><?php print $contact->getPhone(); ?></a>
			</li>
			<li class="table-cell short-cell"><?php print $contact->getMailingCity(); ?></li>
			<li class="table-cell">
				<a href="mailto:<?php print $contact->getEmail(); ?>"><?php print $contact->getEmail(); ?></a>
			</li>
			<li class="table-cell long-cell"><?php print $contact->getAreasOfInterest(); ?></li>
			
		</ul>
	<?php endforeach; ?>


</div>