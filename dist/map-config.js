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
    zoom: 6,
    center: {
      lat: 44.04457,
      lng: -123.09078
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