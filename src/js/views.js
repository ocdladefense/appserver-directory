import views from "./directory.js";
import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
// import * as React from 'react';
// import * as ReactDOM from 'react-dom';
// import { createRoot } from 'react-dom/client'
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
    if (!views[name]) return;
  return views[name].init();
}

function updateView(newNode) {
    //vnode to dom element
    console.log(newNode.type);
    //if (typeof HTMLDivElement) return;
    //console.log(newNode.constructor);
    let elem = View.createElement(newNode);
    
	let container = document.getElementById("view");

    let current = container.cloneNode();
	let parent = container.parentNode;

	current.appendChild(elem);

	return parent.replaceChild(current,container);
}

async function switchView(name) {
	let module, newNode, oldNode;
	if(null == viewCache[name]) {
    	newNode = loadView(name);
	} else {
		newNode = viewCache[name];
	}
    if (!newNode) return;
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

