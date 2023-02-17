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




<script type="module" src="<?php print module_path(); ?>/dist/app.js"></script>


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



<?php if(false && !isset($contacts) || (isset($contacts) && count($contacts) < 1)): ?>
	<ul class="table-row">
		<li>There are no members that meet your search criteria.</li>
	</ul>
<?php endif; ?>



<!-- NOTE: COUNT WILL HAVE TO BE RECALCULATED 
<div>
	<p><?php print "Showing $count members"; ?></p>
</div>
-->

<div id="the-container">
	<div id="show-loading">&nbsp;</div>
	<div id="view">


	</div>
</div>