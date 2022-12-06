/** @jsx vNode */


/**
 * This is our list of components to be used in the app.
**/
import { vNode } from '/node_modules/@ocdladefense/view/view.js';



const Directory = function(props) {
    let contacts = props.entries;
    let activeRecord = props.activeRecord;

    let entries = contacts.map((c) => {return <Entry contact={c} classes={activeRecord == c.Id ? "active" : ""} />});

    return vNode("div",{id:"directory-list"},entries);
};




const Entry = function(props) {

    let c = props.contact;
    let classes = ["entry","entry-directory",props["classes"]];

    let hello = function(e) {
        console.log("Directory entry clicked!");
        let data = e.currentTarget && e.currentTarget.dataset || {};
        e.recordId = data.recordId;
        e.latitude = data.latitude;
        e.longitude = data.longitude;
        e.action = data.action;
    };

    return (
        <div class={classes.join(" ")} data-action="pan" data-longitude={c.MailingAddress.longitude} data-latitude={c.MailingAddress.latitude} data-record-id={c.Id} onclick={hello}>
            <span class="entry-name">
                <a target="_new" href={"/directory/member/"+c.Id}>
                    {c.FirstName + " " + c.LastName}
                </a>
            </span>
            <span class="entry-contact-type">{c.Type}</span>
            <span class="entry-city">{c.MailingAddress.city}, {c.MailingAddress.stateCode}</span>
        </div>
    )
};



function Main(props) {

    let members = props.entries;
    let page = props.page;
    let limit = props.limit;
    let count = props.count;
    let activeRecord = props.activeRecord; 

    let sets = count / limit;
    let pages = [];

    for(var i = 0; i<sets; i++) {
        pages.push(<span data-action="page" data-page={i} class="pager">{""+(i+1)}</span>);
    }

    let directory = vNode(Directory,{entries: members, activeRecord: activeRecord},null);
    let theMap = vNode(Map,{rerender:false},null);

    return (
        <div id="view-container">
            <div id="pager">
                {pages}
            </div>
            {directory}
            {theMap}
        </div>
    )
}



function Map(props) {

  /**
   *           <div id="toolbar" className="navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow">
            <div id="custom"></div>
          </div>
   */

    let container = (
        <div id="map-container">
          <div id="map"></div>
        </div>
    );
  
    return container;
}


export { Directory, Map, Main };