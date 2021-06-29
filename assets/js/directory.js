
let listItems = document.getElementsByClassName("list-item");

for(let i = 0; i < listItems.length; i++){

	listItems[i].addEventListener("click", handleEvent);
}

function handleEvent(e){

	let contactId = e.srcElement.dataset.contactid;

	if(contactId == null) {

		contactId = e.target.parentElement.dataset.contactid;
	}

	let link = document.createElement("a");
	let href = "/directory/members/" + contactId;
	link.setAttribute("href", href);
	link.click();
}
