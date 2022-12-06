import OCDLATheme from "./OCDLATheme.js";

// Get the initial styles (theme) for the map -- OCDLA theme
const startTheme = new OCDLATheme();
const ocdlaInfoWindow = {
  content: `<h1>OCDLA</h1>`
};

// Set up a MapConfiguration object
const config = {
  apiKey: mapKey,
  target: "view",
  mapOptions: {
    zoom: 8,
    center: {
      lat: 44.0521,
      lng: -123.0868
    },
    styles: startTheme.getTheme(),
    defaultMarkerStyles: {
      icon: {
        scaledSize: {
          height: 70,
          width: 80
        }
      }
    },
    ocdlaInfoWindow: ocdlaInfoWindow
  },
  enableHighAccuracy: true
};
export { config };