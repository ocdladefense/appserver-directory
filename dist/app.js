/** @jsx vNode */

import Member from "./Member.js";
import QueryBuilder from "/node_modules/@ocdladefense/query-builder/QueryBuilder.js";
import MapApplication from "/node_modules/@ocdladefense/google-maps/MapApplication.js";
import MapFeature from "/node_modules/@ocdladefense/google-maps/MapFeature.js";
import UrlMarker from "/node_modules/@ocdladefense/google-maps/UrlMarker.js";
import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
import domReady from "/node_modules/@ocdladefense/web/src/web.js";
import { Main } from "./components.js";
// Change to data/prod.js to get actual data from the server.
import loadData from "./data/prod.js";
import { config as mapConfig } from "./map-config.js";

// QueryBuilder instance that will be used
// throughout rendering.
let qb = null;
let myMap = null;
let component = null;
let query = null;

// Execute on page load.
domReady(init);
window.testFullscreen = function () {
  let fstarget = document.getElementById("directory-list").parentNode;
  fstarget.requestFullscreen().then(function (e) {
    let map = document.getElementById("map-container");
    let height = map.offsetHeight;
    map.style.height = height + "px";
  });
};
window.doQueryChange = doQueryChange;
function doQueryChange(e) {
  e.preventDefault();
  let node = document.querySelector("#search-directory");
  let form = new FormData(node);
  let fields = {
    FirstName: QueryBuilder.SQL_LIKE,
    LastName: QueryBuilder.SQL_LIKE,
    Ocdla_Organization__c: QueryBuilder.SQL_LIKE,
    MailingCity: QueryBuilder.SQL_EQ,
    Ocdla_Occupation_Field_Type__c: QueryBuilder.SQL_EQ,
    areaOfInterest: QueryBuilder.SQL_EQ
  };
  let qb = new QueryBuilder(query);
  for (let [key, value] of form) {
    console.log(key, value);
    // Don't add conditions for unspecified fields.
    qb.removeCondition(key);
    if (!value) continue;
    if (!fields[key]) continue;
    let op = fields[key];
    value = op == QueryBuilder.SQL_LIKE ? "%" + value + "%" : value;
    qb.addCondition(key, value, op);
  }
  qb.removeOption("offset");
  console.log(qb);
  return loadData(qb).then(function (result) {
    let offset = query.offset;
    let limit = query.limit;
    let records = result.records;
    let id = null;
    let page = 1;
    console.log(records);
    let members = records.map(member => {
      return Member.fromSObject(member);
    });
    component.update(vNode(Main, {
      entries: members,
      page: page,
      limit: query.limit,
      count: result.count,
      activeRecord: id
    }));
    return members;
  }).then(function (members) {
    updateMap(members);
  });
  return false;
}
function fshandler(e) {
  if (document.fullscreenElement) {
    document.body.classList.add("fullscreen");
  } else {
    document.body.classList.remove("fullscreen");
  }
}
function updateQuery(e) {
  let target = e.target;
  let action = target.dataset && target.dataset.action;
  action = action || e.action;
  let page = target.dataset && target.dataset.page;
  let id = e.recordId;
  let offset = page * qb.getOption("limit");
  if (!["page", "pan"].includes(action)) return;
  if ("pan" == action) {
    let coordinates = {
      latitude: parseFloat(e.latitude),
      longitude: parseFloat(e.longitude)
    };
    console.log(coordinates);
    myMap.pan(coordinates);
    let search = myMap.getFeature("search");
    let marker = search.getMarker(id);
    setTimeout(function () {
      google.maps.event.trigger(marker, 'click');
    }, 300);
    return;
  }
  qb.setOption("offset", offset);
  return loadData(qb).then(function (result) {
    let offset = query.offset;
    let limit = query.limit;
    let records = result.records;
    console.log(records);
    let members = records.map(member => {
      return Member.fromSObject(member);
    });
    component.update(vNode(Main, {
      entries: members,
      page: page,
      limit: query.limit,
      count: result.count,
      activeRecord: id
    }));
    return members;
  }).then(function (members) {
    updateMap(members);
  });
}
function init() {
  query = {
    object: "Contact",
    fields: [],
    where: [],
    limit: 100,
    // Limit to prevent too many markers.
    offset: 0,
    orderBy: "LastName"
  };
  qb = new QueryBuilder(query);
  qb.addCondition("Ocdla_Current_Member_Flag__c", true);
  updateView(qb).then(function (members) {
    showMap(members);
  });
  document.addEventListener("click", updateQuery);
  document.querySelector("#search-directory").addEventListener("submit", doQueryChange);
}
function updateView(qb) {
  // let conditions = query ? JSON.parse(query) : null;
  return loadData(qb).then(function (result) {
    let offset = query.offset;
    let limit = query.limit;
    let records = result.records;
    console.log(records);
    let members = records.map(member => {
      return Member.fromSObject(member);
    });
    component = View.createRoot("#view");
    component.render(vNode(Main, {
      entries: members,
      page: 1,
      limit: query.limit,
      count: result.count
    }));
    // updateView();

    // Fullscreen target.
    let fstarget = document.getElementById("directory-list").parentNode;
    fstarget.addEventListener("fullscreenchange", fshandler);
    return members;
  });
}
function updateMap(members) {
  // Set up the features and load in the data
  let features = {
    search: {
      name: "search",
      label: "search",
      markerLabel: "SE",
      markerStyle: "/modules/maps/assets/markers/members/member-marker-round-black.png",
      // datasource should return a Promise that resolves to an array of objects, each of which should implement the getPosition() method.
      datasource: function () {
        return Promise.resolve(members);
      }
      // datasource: doSearch.bind(null, qb.getObject()),
    }
  };

  myMap.hideFeature("search");
  //create new feature and drop markers
  let searchFeature = new MapFeature(features.search);
  myMap.addFeature(searchFeature);
  searchFeature.loadData();
  searchFeature.loadMarkers().then(() => {
    myMap.showFeature(searchFeature.name);
  });
}
function showMap(members) {
  // Instantiate the app and pass in the mapConfig obj
  myMap = new MapApplication(mapConfig); // Change to "#view"
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
    // The OCDLA icon Info Window is currently being unused.
    let ocdlaIcon = new UrlMarker("/modules/maps/assets/markers/ocdlaMarker/ocdla-marker-round-origLogo.svg");
    myMap.render(ocdlaIcon);

    // Set up the features and load in the data
    let features = {
      search: {
        name: "search",
        label: "search",
        markerLabel: "SE",
        markerStyle: "/modules/maps/assets/markers/members/member-marker-round-black.png",
        // datasource should return a Promise that resolves to an array of objects, each of which should implement the getPosition() method.
        datasource: function () {
          return Promise.resolve(members);
        }
        // datasource: doSearch.bind(null, qb.getObject()),
      }
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
window.init = init;
export default init;