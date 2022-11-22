function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
/**@jsx vNode*/

import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
import MapApplication from "/node_modules/@ocdladefense/google-maps/MapApplication.js";
import MapFeature from "/node_modules/@ocdladefense/google-maps/MapFeature.js";
import UrlMarker from "/node_modules/@ocdladefense/google-maps/UrlMarker.js";
import QueryBuilder from "/node_modules/@ocdladefense/query-builder/QueryBuilder.js";
// import {FileUploadService,FileUploadComponent} from "/node_modules/@ocdladefense/node-file-upload/Upload.js";

console.log("Directory module loaded.");
var userQuery = {
  object: "Contact",
  fields: [],
  where: [],
  limit: 200 //limit to stop too many markers?
};

//Query building with npm package
var qb = new QueryBuilder(userQuery);
var conditions = JSON.parse(query);
console.log(conditions);
var _iterator = _createForOfIteratorHelper(conditions),
  _step;
try {
  for (_iterator.s(); !(_step = _iterator.n()).done;) {
    var con = _step.value;
    var c = {
      field: con.fieldname,
      op: con.op,
      value: con.value
    };
    qb.addCondition(c);
  }
} catch (err) {
  _iterator.e(err);
} finally {
  _iterator.f();
}
var currentMembers = {
  field: "Ocdla_Current_Member_Flag__c",
  op: QueryBuilder.SQL_EQ,
  value: true,
  editable: false
};
qb.updateCondition(currentMembers);
console.log(qb.getObject());
document.addEventListener("viewswitch", function () {
  if ("map" == view) {
    showMap();
    qb.render();
  }
});

// Get the initial styles (theme) for the map -- OCDLA theme
var startTheme = new OCDLATheme();
var ocdlaInfoWindow = {
  content: "<h1>OCDLA</h1>"
};

// Set up a MapConfiguration object
var config = {
  apiKey: Keys.mapKey,
  target: "view",
  mapOptions: {
    zoom: 6,
    center: {
      lat: 44.04457,
      lng: -123.09078
    },
    styles: startTheme.getTheme(),
    defaultMarkerStyles: {
      icon: {
        scaledSize: {
          height: 70,
          width: 80
        }
      }
    },
    ocdlaInfoWindow: ocdlaInfoWindow
  },
  enableHighAccuracy: true
};
function showMap() {
  // Instantiate the app and pass in the mapConfig obj
  var myMap = new MapApplication(config); // Change to "#view"
  window.myMap = myMap;
  qb.render("custom");
  // Listen for changes to the underlying query UX.
  document.addEventListener("querychange", contactQuery, true);
  function contactQuery(e) {
    var query = e.detail;
    var searchFeature = myMap.getFeature("search");

    //need to clear markers?
    searchFeature.setDatasource(doSearch.bind(null, query));

    // Load the feature's data.
    searchFeature.loadData();

    // Load the feature's markers.
    searchFeature.loadMarkers().then(function () {
      //show the feature
    });
    //searchFeature.markers = [];
    //shows all search results after 1 box, currently the search query is only added to
  }

  // Render the map to the page
  myMap.init().then(function () {
    // The OCDLA icon Info Window is currently being unused.
    var ocdlaIcon = new UrlMarker("/modules/maps/assets/markers/ocdlaMarker/ocdla-marker-round-origLogo.svg");
    myMap.render(ocdlaIcon);

    // Set up the features and load in the data
    var features = {
      search: {
        name: "search",
        label: "search",
        markerLabel: "SE",
        markerStyle: "/modules/maps/assets/markers/members/member-marker-round-black.png",
        datasource: doSearch.bind(null, qb.getObject())
      }
    };
    //create new feature and drop markers
    var searchFeature = new MapFeature(features.search);
    myMap.addFeature(searchFeature);
    searchFeature.loadData();
    searchFeature.loadMarkers().then(function () {
      myMap.showFeature(searchFeature.name);
    });
  });
}
var views = {
  map: {
    init: initView,
    render: showMap
  }
};
function doSearch(qb) {
  var body = JSON.stringify(qb);
  return fetch("/maps/search", {
    method: "POST",
    // *GET, POST, PUT, DELETE, etc.
    mode: "cors",
    // no-cors, *cors, same-origin
    cache: "no-cache",
    // *default, no-cache, reload, force-cache, only-if-cached
    credentials: "same-origin",
    // include, *same-origin, omit
    headers: {
      "Content-Type": "application/json",
      Accept: "text/html"
      // 'Content-Type': 'application/x-www-form-urlencoded',
    },

    body: body
  }).then(function (resp) {
    return resp.json();
  }).then(function (queryAndResults) {
    var members = queryAndResults.results;
    return members.map(function (member) {
      var newMember = new Member(member);
      return newMember;
    });
  });
}
function initView(name) {
  if ("list" == name) return null;
  var container = vNode("div", {
    id: "view",
    "class": "view view-block"
  }, vNode("div", {
    id: "map-container"
  }, vNode("div", {
    id: "toolbar",
    className: "navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow"
  }, vNode("div", {
    id: "custom"
  })), vNode("div", {
    id: "map"
  })));
  return container;
}
export default views;