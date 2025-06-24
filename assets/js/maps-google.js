'use strict';

(function ($) {
  'use strict';

  // Default

  new GMaps({
    el: '#default',
    lat: -12.043333,
    lng: -77.028333,
    zoomControl: true,
    zoomControlOpt: {
      style: 'SMALL',
      position: 'TOP_LEFT'
    },
    panControl: false,
    streetViewControl: false,
    mapTypeControl: false,
    overviewMapControl: false
  });

  // Styled
  var styledMap = new GMaps({
    el: "#styled",
    lat: 41.895465,
    lng: 12.482324,
    zoom: 5,
    zoomControl: true,
    zoomControlOpt: {
      style: "SMALL",
      position: "TOP_LEFT"
    },
    panControl: true,
    streetViewControl: false,
    mapTypeControl: false,
    overviewMapControl: false
  });

  var styles = [{
    stylers: [{ hue: "#43b968" }, { saturation: -40 }]
  }, {
    featureType: "road",
    elementType: "geometry",
    stylers: [{ lightness: 100 }, { visibility: "simplified" }]
  }, {
    featureType: "road",
    elementType: "labels",
    stylers: [{ visibility: "off" }]
  }];

  styledMap.addStyle({
    styledMapName: "Styled Map",
    styles: styles,
    mapTypeId: "map_style"
  });

  styledMap.setStyle("map_style");

  //Routes
  var routesMap = new GMaps({
    el: '#routes',
    lat: -12.043333,
    lng: -77.028333
  });
  routesMap.drawRoute({
    origin: [-12.044012922866312, -77.02470665341184],
    destination: [-12.090814532191756, -77.02271108990476],
    travelMode: 'driving',
    strokeColor: '#131540',
    strokeOpacity: 0.6,
    strokeWeight: 6
  });

  // Overlays
  routesMap = new GMaps({
    el: '#overlays',
    lat: -12.043333,
    lng: -77.028333
  });
  routesMap.drawOverlay({
    lat: routesMap.getCenter().lat(),
    lng: routesMap.getCenter().lng(),
    layer: 'overlayLayer',
    content: '<div class="alert alert-danger">Location</div>',
    verticalAlign: 'top',
    horizontalAlign: 'center'
  });
})(jQuery);
