<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />


<script src="<?php print module_path(); ?>/assets/js/mapkey.js"></script>
<script src="<?php print module_path(); ?>/assets/js/Member.js"></script>
<script src="<?php print module_path(); ?>/assets/js/OCDLATheme.js"></script>
<script type="module" src="<?php print module_path(); ?>/assets/js/views.js"></script>

<style>
    #map-container, #map {
        height: 600px;
        width: 600px;
    }
    </style>

<div>
	<h2>OCDLA Member Directory</h2>
</div>

<div class="search">
	<?php print $search; ?>
</div>

<?php if(is_admin_user()): ?>

<div>
	<strong><?php print $query; ?></strong>
</div>

<?php endif; ?>

<input type="hidden" id="conditions" value='<?php echo $conditions; ?>' />

<button onclick="window.switchView('map')">Map View</button>

<div id="custom"></div>
<div id="filters"></div>
<!-- NOTE: COUNT WILL HAVE TO BE RECALCULATED -->
<div>
	<p><?php print "Showing $count members"; ?></p>
</div>

<div id="view" class="view table view-table">
	<tbody>

		<ul class="table-row">
			<li class="table-header">Name</li>
			<li class="table-header">Field</li>
			<li class="table-header">Organization</li>
			<li class="table-header">Phone</li>
			<li class="table-header">City</li>
			<li class="table-header">Email</li>
			<li class="table-header">Areas of Interest</li>
		</ul>

			
		<?php if(!isset($contacts) || (isset($contacts) && count($contacts) < 1)): ?>
			<ul class="table-row">
				<li>There are members that meet your search criteria.</li>
			</ul>
			
		<?php else: ?>
		
			<?php foreach($contacts as $contact):
				$areasOfInterest = $contact->getAreasOfInterest();
			?>

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
		<?php endif; ?>
	</tbody>
</table>


<script>


</script>