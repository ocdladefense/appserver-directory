const Member = (function () {


	function Member(contact) {
		this.status = contact.Ocdla_Member_Status__c;
		this.name = contact.Name;
		this.firstName = contact.firstName;
		this.lastName = contact.lastName;
		this.primary = contact.Ocdla_Expert_Witness_Primary__c;
		this.email = contact.Email;
		this.phone = contact.Phone;
		this.mailingAddress = contact.MailingAddress || "";
		this.position = { lat: contact.MailingAddress.latitude, lng: contact.MailingAddress.longitude };
		this.type = "Member";
	}


	var prototype = {
		getPosition: function () { return this.position; },
	};
	Member.prototype = prototype;

	return Member;
})();