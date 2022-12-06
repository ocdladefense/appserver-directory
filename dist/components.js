/** @jsx vNode */

/**
 * This is our list of components to be used in the app.
**/
import { vNode } from '/node_modules/@ocdladefense/view/view.js';
const Directory = function (props) {
  let contacts = props.entries;
  let activeRecord = props.activeRecord;
  let entries = contacts.map(c => {
    return vNode(Entry, {
      contact: c,
      classes: activeRecord == c.Id ? "active" : ""
    });
  });
  return vNode("div", {
    id: "directory-list"
  }, entries);
};
const Entry = function (props) {
  let c = props.contact;
  let classes = ["entry", "entry-directory", props["classes"]];
  let hello = function (e) {
    console.log("Directory entry clicked!");
    let data = e.currentTarget && e.currentTarget.dataset || {};
    e.recordId = data.recordId;
    e.latitude = data.latitude;
    e.longitude = data.longitude;
    e.action = data.action;
  };
  return vNode("div", {
    class: classes.join(" "),
    "data-action": "pan",
    "data-longitude": c.MailingAddress.longitude,
    "data-latitude": c.MailingAddress.latitude,
    "data-record-id": c.Id,
    onclick: hello
  }, vNode("span", {
    class: "entry-name"
  }, vNode("a", {
    target: "_new",
    href: "/directory/member/" + c.Id
  }, c.FirstName + " " + c.LastName)), vNode("span", {
    class: "entry-contact-type"
  }, c.Type), vNode("span", {
    class: "entry-city"
  }, c.MailingAddress.city, ", ", c.MailingAddress.stateCode));
};
function Main(props) {
  let members = props.entries;
  let page = props.page;
  let limit = props.limit;
  let count = props.count;
  let activeRecord = props.activeRecord;
  let sets = count / limit;
  let pages = [];
  for (var i = 0; i < sets; i++) {
    pages.push(vNode("span", {
      "data-action": "page",
      "data-page": i,
      class: "pager"
    }, "" + (i + 1)));
  }
  let directory = vNode(Directory, {
    entries: members,
    activeRecord: activeRecord
  }, null);
  let theMap = vNode(Map, {
    rerender: false
  }, null);
  return vNode("div", {
    id: "view-container"
  }, vNode("div", {
    id: "pager"
  }, pages), directory, theMap);
}
function Map(props) {
  /**
   *           <div id="toolbar" className="navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow">
            <div id="custom"></div>
          </div>
   */

  let container = vNode("div", {
    id: "map-container"
  }, vNode("div", {
    id: "map"
  }));
  return container;
}
export { Directory, Map, Main };