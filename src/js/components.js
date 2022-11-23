/** @jsx vNode */


/**
 * This is our list of components to be used in the app.
**/
import { vNode } from '/node_modules/@ocdladefense/view/view.js';



const Directory = function(props) {
    let contacts = props.entries;

    let entries = contacts.map((c) => {
        let name = vNode("span",{"class":"entry-name"},(c.FirstName + " " + c.LastName));
        let org = vNode("span",{"class":"entry-contact-type"},c.Type);
        let city = vNode("span",{"class":"entry-city"},c.MailingCity);
        return vNode("div",{"class":"entry entry-directory"},[name,org,city]);
    });

    return vNode("div",{},entries);

    /*
    return (
        <div>
            {entries}
        </div>
    )
    */
};

export default Directory;