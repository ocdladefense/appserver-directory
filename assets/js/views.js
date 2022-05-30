import views from "/modules/directory/assets/js/directory.js";
//console.log(views);
//import { renderMap, initializeMap } from "./map.js";
/**
 * To test this code execute either:
 * switchView("list");
 * switchView("map");
 */
const viewCache = {}; 

let currentView = "list";

function loadView(name) {
  return views[name].init();
}

function updateView(newNode) {

	let container = document.getElementById("view");
	let current = container.cloneNode();
	let parent = container.parentNode;

	current.appendChild(newNode);

	return parent.replaceChild(current,container);
}

async function switchView(name) {
	let module, newNode, oldNode;
	if(null == viewCache[name]) {
    	newNode = loadView(name);
	} else {
		newNode = viewCache[name];
	}

    oldNode = updateView(newNode);

	// Place the old domtree in the cache,
	// for later use, i.e., when we 
	// switch back to list view.
	viewCache[currentView] = oldNode;
	currentView = name;
	// Post-render operations.
	// For example, execute any init
	// function associated with the newly-loaded module.
	// qb.render("custom");
    if (name == 'map')
    {
        views[name].render();
    }
}

window.switchView = switchView;

export default switchView;

