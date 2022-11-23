const Member = (function () {


	function Member(contact) {
		for(var prop in contact) {
			this[prop] = contact[prop];
		}
		this.type = "Member";
	}


	var prototype = {
		getPosition: function () { return this.position; },
	};

	Member.prototype = prototype;

	Member.fromSObject = function(contact) {
		let record = {};
		record.MemberStatus = contact.Ocdla_Member_Status__c;
		record.Name = contact.Name;
		record.FirstName = contact.FirstName;
		record.LastName = contact.LastName;
		record.primary = contact.Ocdla_Expert_Witness_Primary__c;
		record.Email = contact.Email;
		record.Phone = contact.Phone;
		record.MailingAddress = contact.MailingAddress || "";
		record.Type = contact.Ocdla_Occupation_Field_Type__c;
		record.Organization = contact.Ocdla_Organization__c;
		
		if(contact.MailingAddress) {
			record.position = { lat: contact.MailingAddress.latitude, lng: contact.MailingAddress.longitude };
		} else {
			record.position = null;
		}
		record.type = "Member";

		return new Member(record);
	};

	return Member;
})();