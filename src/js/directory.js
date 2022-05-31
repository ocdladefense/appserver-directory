/**@jsx vNode*/
/**
 * Defines code to be executed when the view changes.
 *
 */

import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
//import OCDLACustom from "/node_modules/@ocdladefense/node-...
import MapApplication from "/node_modules/@ocdladefense/google-maps/MapApplication.js";
import MapFeature from "/node_modules/@ocdladefense/google-maps/MapFeature.js";
import UrlMarker from "/node_modules/@ocdladefense/google-maps/UrlMarker.js";
import QueryBuilder from "/node_modules/@ocdladefense/query-builder/QueryBuilder.js";
// import {FileUploadService,FileUploadComponent} from "/node_modules/@ocdladefense/node-file-upload/Upload.js";

console.log("Directory module loaded.");

const userQuery = {
  object: "Contact",
  fields: [],
  where: [],
  limit: 200, //limit to stop too many markers?
};

//Query building with npm package
let qb = new QueryBuilder(userQuery);
let conditions = JSON.parse(document.getElementById("conditions").value);

console.log(conditions);

for (let con of conditions) {
  let c = {
    field: con.fieldname,
    op: con.op,
    value: con.value,
  };
  qb.addCondition(c);
}

console.log(qb.getObject());

document.addEventListener("viewswitch", () => {
  if ("map" == view) {
    showMap();

    qb.render();
  }
});

// Get the initial styles (theme) for the map -- OCDLA theme
const startTheme = new OCDLATheme();

const ocdlaInfoWindow = {
  content: `<h1>OCDLA</h1>`,
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
        },
      },
    },
    ocdlaInfoWindow: ocdlaInfoWindow,
  },
  enableHighAccuracy: true,
};

function showMap() {
  // Instantiate the app and pass in the mapConfig obj
  const myMap = new MapApplication(config); // Change to "#view"
  window.myMap = myMap;

  qb.render("custom");
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
  });
}
const views = {
  map: {
    init: initView,
    render: showMap,
  },
};

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

function initView(name) {
  if ("list" == name) return null;

  let container = (
    <div id="map-container">
      <div
        id="toolbar"
        className="navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow"
      >
        <div id="custom"></div>
      </div>
      <div id="map"></div>
    </div>
  );

  return container;
}

export default views;
