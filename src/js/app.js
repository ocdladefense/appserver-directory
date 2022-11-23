// import views from "./directory.js";
import { vNode, View } from "/node_modules/@ocdladefense/view/view.js";
import domReady from "/node_modules/@ocdladefense/web/src/web.js";
import Directory from "./components.js";


// Execute on page load.
domReady(init);


function init() {

	/*
	return fetch("/maps/search", {
		method: "POST", // *GET, POST, PUT, DELETE, etc.
		mode: "cors", // no-cors, *cors, same-origin
		cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
		credentials: "same-origin", // include, *same-origin, omit
		headers: {
			"Content-Type": "application/json",
			Accept: "text/html"
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
	*/

	loadData().then(function(data) {
		updateView(vNode(Directory,{entries: data},null));
	});

}


function loadData() {

	let entries = [
		{
		   FirstName: "José",
		   LastName: "Bernal",
		   Type: "Attorney at Law",
		   MailingCity: "Eugene"
		},
		{
		   FirstName: "Autumn",
		   LastName: "Bernal",
		   Type: "Investigator",
		   MailingCity: "Eugene"
		},
		{
		   FirstName: "Noella",
		   LastName: "Bernal",
		   Type: "Public Defender",
		   MailingCity: "Eugene"
		},
		{
		   FirstName: "Renita",
		   LastName: "Bernal",
		   Type: "Attorney at Law",
		   MailingCity: "Eugene"
		}
	   ];
	
	return Promise.resolve(entries);
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

