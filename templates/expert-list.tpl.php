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

<div>
	<h2>OCDLA Expert Witness Directory</h2>
</div>

<div class="search">
	<?php print $search; ?>
</div>

<?php if($user->isAdmin()) : ?>
	<div>
		<strong><?php print $query; ?></strong>
	</div>
<?php endif; ?>








<div>
	<p><?php print "Showing $count expert witnesses"; ?></p>
</div>

<div class="table">

	<ul class="table-row">
		<li class="table-header">Name</li>
		<li class="table-header">Primary Field</li>
		<li class="table-header">Organization</li>
		<li class="table-header">Phone</li>
		<li class="table-header">City</li>
		<li class="table-header">Email</li>
		<li class="table-header">Other Areas</li>
	</ul>
		
	<?php foreach($experts as $expert): ?>
		<?php $areasOfInterest = $expert->getAreasOfInterest(); ?>

		<ul class="table-row"> 

					<li class="table-cell">
						<a href="/directory/experts/<?php print $expert->getId(); ?>"><?php print $expert->getName(); ?></a>
					</li>
					<li class="table-cell long-cell"><?php print $expert->getPrimaryFields(); ?></li>
					<li class="table-cell"><?php print $expert->getOcdlaOrganization(); ?></li>
					<li class="table-cell short-cell">
						<a href="tel:<?php print $expert->getPhoneNumericOnly(); ?>"><?php print $expert->getPhone(); ?></a>
					</li>
					<li class="table-cell short-cell"><?php print $expert->getMailingCity(); ?></li>
					<li class="table-cell">
						<a href="mailto:<?php print $expert->getEmail(); ?>"><?php print $expert->getEmail(); ?></a>
					</li>
					<li class="table-cell long-cell"><?php print $expert->getExpertWitnessOtherAreas(); ?></li>
					
				</ul>
	<?php endforeach; ?>

</div>