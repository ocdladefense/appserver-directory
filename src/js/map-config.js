import OCDLATheme from "./OCDLATheme.js";



// Get the initial styles (theme) for the map -- OCDLA theme
const startTheme = new OCDLATheme();

const ocdlaInfoWindow = {
  content: `<h1>OCDLA</h1>`,
};

// Set up a MapConfiguration object
const config = {
  apiKey: mapKey,
  target: "view",
  mapOptions: {
    zoom: 7,
    center: {
      lat: 44.0521,
      lng: -120.8868
    },
    styles: startTheme.getTheme(),
    defaultMarkerStyles: {
      icon: {
        scaledSize: {
          height: 70,
          width: 80,
        },
      },
    },
    ocdlaInfoWindow: ocdlaInfoWindow,
  },
  enableHighAccuracy: true,
};


export {config};

