// import views from "./directory.js";
import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
import QueryBuilder from "/node_modules/@ocdladefense/query-builder/QueryBuilder.js";
import domReady from "/node_modules/@ocdladefense/web/src/web.js";
import MapApplication from "/node_modules/@ocdladefense/google-maps/MapApplication.js";
import MapFeature from "/node_modules/@ocdladefense/google-maps/MapFeature.js";
import UrlMarker from "/node_modules/@ocdladefense/google-maps/UrlMarker.js";
import {Directory, Map} from "./components.js";
import loadData from "./data/prod.js"; // change to data/prod.js to get actual data from the server.


// Execute on page load.
// domReady(init);


window.testQuery = testQuery;

function testQuery() {

	const userQuery = {
		object: "Contact",
		fields: [],
		where: [],
		limit: 25, // Limit to prevent too many markers.
	};
	
	let qb = new QueryBuilder(userQuery);
	let conditions = query ? JSON.parse(query) : {};
	
	
	for (let con of conditions) {
		let c = {
			field: con.fieldname,
			op: con.op,
			value: con.value,
		};
		qb.addCondition(c);
	}
	
	let currentMembers = {
		field: "Ocdla_Current_Member_Flag__c",
		op: QueryBuilder.SQL_EQ,
		value: true,
		editable: false
	};

	qb.updateCondition(currentMembers);

	loadData(qb)
	.then(function(records) {
		console.log(records);

		let members = records.map((member) => {
			return Member.fromSObject(member);
		});

		let directory = vNode(Directory,{entries: members},null);
		let map = vNode(Map,{},null);

		updateView(vNode("div",{},[directory,map]));

		showMap();
	});
}






function init() {

	loadData().then(function(data) {

		let members = data.map((member) => {
			let newMember = new Member(member);
			return newMember;
		});

		updateView(vNode(Directory,{entries: members},null));
	});

}





function showMap() {
	// Instantiate the app and pass in the mapConfig obj
	const myMap = new MapApplication(config); // Change to "#view"
	window.myMap = myMap;
  
	// qb.render("custom");
	// Listen for changes to the underlying query UX.
	document.addEventListener("querychange", contactQuery, true);
  
	function contactQuery(e) {
	  let query = e.detail;
  
	  let searchFeature = myMap.getFeature("search");
  
	  //need to clear markers?
	  searchFeature.setDatasource(doSearch.bind(null, query));
  
	  // Load the feature's data.
	  searchFeature.loadData();
  
	  // Load the feature's markers.
	  searchFeature.loadMarkers().then(() => {
		//show the feature
	  });
	  //searchFeature.markers = [];
	  //shows all search results after 1 box, currently the search query is only added to
	}
  
	// Render the map to the page
	myMap.init().then(() => {
	  
		/*
		// The OCDLA icon Info Window is currently being unused.
	  let ocdlaIcon = new UrlMarker(
		"/modules/maps/assets/markers/ocdlaMarker/ocdla-marker-round-origLogo.svg"
	  );
	  myMap.render(ocdlaIcon);
  
	  // Set up the features and load in the data
	  let features = {
		search: {
		  name: "search",
		  label: "search",
		  markerLabel: "SE",
		  markerStyle:
			"/modules/maps/assets/markers/members/member-marker-round-black.png",
		  datasource: doSearch.bind(null, qb.getObject()),
		},
	  };
	  //create new feature and drop markers
	  let searchFeature = new MapFeature(features.search);
	  myMap.addFeature(searchFeature);
	  searchFeature.loadData();
	  searchFeature.loadMarkers().then(() => {
		myMap.showFeature(searchFeature.name);
	  });
	  */
	});
  }





function updateView(newNode) {
    //vnode to dom element

    let isdom = newNode.constructor.name.indexOf("HTML") !== -1;

    console.log(newNode.constructor.name);

	let container = document.getElementById("view");
	let parent = container.parentNode;

    let elem = isdom ? newNode : View.createElement(newNode);

	return parent.replaceChild(elem,container);
}



window.init = init;

export default init;

