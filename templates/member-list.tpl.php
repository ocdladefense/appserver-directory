<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />
<script type="module" src="<?php print module_path(); ?>/assets/js/map.js"></script>
<script src="<?php print module_path(); ?>/assets/js/mapkey.js"></script>
<script src="<?php print module_path(); ?>/assets/js/Member.js"></script>
<script src="<?php print module_path(); ?>/assets/js/OCDLATheme.js"></script>
<script>
    const conditions = JSON.parse(<?php print $conditions?>;);
    </script>
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

<!-- <form action="/maps" method="post">
	<input type="hidden" name="query" value="<?php echo $query ?>" />
	<input type="submit" value="Map View" />
</form> -->
<button onclick="switchView('map')">Map View</button>

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

async function loadModule(name) {
	name = name || "map";
	let module = await import('/modules/directory/assets/js/map.js');
	return module;
}

async function updateView(newNode) {

	let container = document.getElementById("view");
	let current = container.cloneNode();
	let parent = container.parentNode;

	current.appendChild(newNode);

	parent.replaceChild(current,container);
	
}

async function switchView(name) {
	let module = await loadModule(name);
	let node = module.render(); // Should return a new DOM tree.
	updateView(node);
}
function createElements()
{
    let stage = document.createElement("div");
    stage.setAttribute("id","mapContainer");
    let toolbar = document.createElement("div");
    toolbar.setAttribute("id","toolbar");
    toolbar.setAttribute("class","navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow");
    let map = document.createElement("div");
    map.setAttribute("id","map");
    stage.appendChild(toolbar);
    stage.appendChild(map);
    return stage;
    
}

function test() {
	let newNode = createElements();
    updateView(newNode);
    switchView("map");
}


</script>