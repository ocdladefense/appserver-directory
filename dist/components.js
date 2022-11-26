/** @jsx vNode */

/**
 * This is our list of components to be used in the app.
**/
import { vNode } from '/node_modules/@ocdladefense/view/view.js';
const Directory = function (props) {
  let contacts = props.entries;
  let entries = contacts.map(c => {
    let name = vNode("span", {
      "class": "entry-name"
    }, c.FirstName + " " + c.LastName);
    let org = vNode("span", {
      "class": "entry-contact-type"
    }, c.Type);
    let city = vNode("span", {
      "class": "entry-city"
    }, c.MailingAddress.city);
    return vNode("div", {
      "class": "entry entry-directory"
    }, [name, org, city]);
  });
  return vNode("div", {
    id: "directory-list"
  }, entries);

  /*
  return (
      <div>
          {entries}
      </div>
  )
  */
};

function Map(name) {
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
export { Directory, Map };