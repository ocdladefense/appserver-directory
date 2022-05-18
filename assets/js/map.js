/**
 * Main entry point to initialize a MapApplication.
 *
 * MapApplication will consume one or more datasources/Callouts from
 *  the MapDatasource repository and one or more MapFeatures.
 */
//import { config, mapinit, features } from "./config";
import MapApplication from "../../node_modules/@ocdladefense/google-maps/MapApplication.js";
import QueryBuilder from "../../node_modules/@ashirk94/query-builder/QueryBuilder.js";
import UrlMarker from "../../node_modules/@ocdladefense/google-maps/UrlMarker.js";
//import MapFeature

// Instantiate the app and pass in the mapConfig obj
const myMap = new MapApplication(config);
window.myMap = myMap;
// Render the map to the page
// After the map finished initializing, get and set the users

//mock soql query components
const SQL_EQ = "=";
const SQL_LIKE = "LIKE";
const SQL_GT = ">";
const SQL_LT = "<";

let c1 = { field: "LastName", value: "Smith", op: SQL_EQ };
let c2 = { field: "Ocdla_Member_Status__c", value: "R", op: SQL_EQ };
//let c3 = { field: "FirstName", value: "Gerry"};

const userQuery = {
  object: "Contact",
  fields: [],
  where: [c1, c2],
  limit: 20,
};
//custom event
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

//Query building with npm package
let qb = new QueryBuilder(userQuery);
qb.render("custom");

myMap.init(mapinit).then(function () {
  //Hides the filters until data is loaded

  myMap.hideFilters();
  //console.log("map loaded");

  // The OCDLA icon Info Window is currently being unused
  //
  let ocdlaIcon = new UrlMarker(
    "/modules/maps/assets/markers/ocdlaMarker/ocdla-marker-round-origLogo.svg"
  );
  myMap.render(ocdlaIcon);

  // Set up the features and load in the data

  let config = {
    name: "search",
    label: "search",
    markerLabel: "SE",
    markerStyle:
      "/modules/maps/assets/markers/members/member-marker-round-black.png",
    datasource: doSearch.bind(null, qb.getObject()),
  };

  //qb.onQueryUpdate(contactQuery);
  features["search"] = config;
  myMap.loadFeatures(features);
  myMap.loadFeatureData();
});

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
//Add event listener
document.addEventListener("click", handleEvent, true);
