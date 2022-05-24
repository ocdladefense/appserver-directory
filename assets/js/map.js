/**
 * Main entry point to initialize a MapApplication.
 *
 * MapApplication will consume one or more datasources/Callouts from
 *  the MapDatasource repository and one or more MapFeatures.
 */

import MapApplication from "/node_modules/@ocdladefense/google-maps/MapApplication.js";
import QueryBuilder from "/node_modules/@ocdladefense/query-builder/QueryBuilder.js";
import UrlMarker from "/node_modules/@ocdladefense/google-maps/UrlMarker.js";



// Get the initial styles (theme) for the map -- OCDLA theme
const startTheme = new OCDLATheme();

const ocdlaInfoWindow = {
  content: `<h1>OCDLA</h1>`
};


// Set up a MapConfiguration object
const config = {
  apiKey: Keys.mapKey,
  target: "view",
  mapOptions: {
    zoom: 6,
    center: {
      lat: 44.04457,
      lng: -123.09078,
    },
    styles: startTheme.getTheme(),
    defaultMarkerStyles: {
      icon: {
        scaledSize: {
          height: 70,
          width: 80,
        }
      }
    },
    ocdlaInfoWindow: ocdlaInfoWindow,
  },
  enableHighAccuracy: true
};

// Instantiate the app and pass in the mapConfig obj
const myMap = new MapApplication(config); // Change to "#view"
window.myMap = myMap;



let c1 = { field: "LastName", value: "Smith", op: QueryBuilder.SQL_EQ };
let c2 = { field: "Ocdla_Member_Status__c", value: "R", op: QueryBuilder.SQL_EQ };

const userQuery = {
  object: "Contact",
  fields: [],
  where: [],
  limit: 20
};

//Query building with npm package
let qb = new QueryBuilder(userQuery);

let conditions = JSON.parse(document.getElementById("conditions").value);

console.log(conditions);


for (let condition of conditions)
{
    let c = {
        field: condition.fieldname,
        op: condition.op,
        value: condition.value
    };
    qb.addCondition(c);
}
console.log(qb.getObject());
//renders checkboxes
qb.render("custom");



// Set up the map legend UX.
document.addEventListener("click", handleEvent, true);

// Listen for changes to the underlying query UX.
document.addEventListener("querychange", contactQuery, true);



function contactQuery(e) {
    console.log(e);
  let query = e.detail;

  let searchFeature = myMap.getFeature("search");

  //need to clear markers?
  searchFeature.setDatasource(doSearch.bind(null, e.detail));

  // Load the feature's data.
  searchFeature.loadData();

  // Load the feature's markers.
  searchFeature.loadMarkers().then(() => {
    //show the feature
  });
  //searchFeature.markers = [];
  //shows all search results after 1 box, currently the search query is only added to
}

function init() {
// Render the map to the page
myMap.init().then(function () {
    let features = {};
    // Hides the filters until data is loaded.
  
    myMap.hideFilters();
    // console.log("map loaded");
  
    // The OCDLA icon Info Window is currently being unused.
    let ocdlaIcon = new UrlMarker(
      "/modules/maps/assets/markers/ocdlaMarker/ocdla-marker-round-origLogo.svg"
    );
    myMap.render(ocdlaIcon);
  
    // Set up the features and load in the data
    let config = {
      name: "search",
      label: "search",
      markerLabel: "SE",
      markerStyle: "/modules/maps/assets/markers/members/member-marker-round-black.png",
      datasource: doSearch.bind(null, qb.getObject()),
    };
  
  
    features["search"] = config;
    myMap.loadFeatures(features);
    myMap.loadFeatureData();
    myMap.showFeature('search');
  });
}


//get data
function doSearch(qb) {
  let body = JSON.stringify(qb);

  return fetch("/maps/search", {
    method: "POST", // *GET, POST, PUT, DELETE, etc.
    mode: "cors", // no-cors, *cors, same-origin
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    credentials: "same-origin", // include, *same-origin, omit
    headers: {
      "Content-Type": "application/json",
      Accept: "text/html",
      // 'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: body,
  })
    .then((resp) => {
      return resp.json();
    })
    .then((queryAndResults) => {
      let members = queryAndResults.results;
      return members.map((member) => {
        let newMember = new Member(member);
        return newMember;
      });
    });
}
/**
 * Let the user turn features on and off.
 */
function handleEvent(e) {
  var target = e.target;

  var featureName = target.dataset && target.dataset.featureName;
  if (!featureName || target.classList.contains("query-filter")) return;

  //console.log(featureName);

  if (myMap.isVisible(featureName)) {
    target.classList.remove("feature-active");
    myMap.hideFeature(featureName);
  } else {
    myMap.showFeature(featureName);
    target.classList.add("feature-active");
  }
}

function render()
{
    let stage = document.createElement("div");
    stage.setAttribute("id","map-container");
    let toolbar = document.createElement("div");
    toolbar.setAttribute("id","toolbar");
    toolbar.setAttribute("class","navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow");
    let map = document.createElement("div");
    map.setAttribute("id","map");
    stage.appendChild(toolbar);
    stage.appendChild(map);
    return stage;
}

export {render, init};