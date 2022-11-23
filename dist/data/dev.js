function loadData() {
  let entries = [{
    FirstName: "José",
    LastName: "Bernal",
    MemberStatus: "R",
    Type: "Attorney at Law",
    MailingAddress: {
      City: "Portland",
      State: "Oregon",
      PostalCode: "97209",
      Latitude: 45.5152,
      Longitude: 122.6784
    }
  }, {
    FirstName: "Autumn",
    LastName: "Bernal",
    MemberStatus: "R",
    Type: "Investigator",
    MailingAddress: {
      City: "Portland",
      State: "Oregon",
      PostalCode: "97209",
      Latitude: 45.5152,
      Longitude: 122.6784
    }
  }, {
    FirstName: "Noella",
    LastName: "Bernal",
    MemberStatus: "R",
    Type: "Public Defender",
    MailingAddress: {
      City: "Portland",
      State: "Oregon",
      PostalCode: "97209",
      Latitude: 45.5152,
      Longitude: 122.6784
    }
  }, {
    FirstName: "Renita",
    LastName: "Bernal",
    MemberStatus: "R",
    Type: "Attorney at Law",
    MailingAddress: {
      City: "Portland",
      State: "Oregon",
      PostalCode: "97209",
      Latitude: 45.5152,
      Longitude: 122.6784
    }
  }];
  return Promise.resolve(entries);
}
export default loadData;