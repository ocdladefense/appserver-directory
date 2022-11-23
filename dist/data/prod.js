function loadData(qb) {
  console.log(qb);
  let body = JSON.stringify(qb.getObject());
  let opts = {
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
      Accept: "application/json"
    },
    body: body
  };
  return fetch("/maps/search", opts).then(resp => {
    return resp.json();
  }).then(queryAndResults => {
    return queryAndResults.results;
  });
}
export default loadData;