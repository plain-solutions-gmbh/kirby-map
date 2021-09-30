import Map from "./components/Map.vue";
import Marker from "./components/Marker.vue";
import Geolocation from "./components/Geolocation.vue";

window.panel.plugin("microman/map", {
  icons: {
    marker:
      '<path d="M7.3,15.6C2.8,9.1,2,8.4,2,6.1c0-3.3,2.7-5.9,5.9-5.9s5.9,2.7,5.9,5.9c0,2.4-0.8,3.1-5.3,9.6C8.3,16.1,7.6,16.1,7.3,15.6L7.3,15.6z M7.9,8.5c1.4,0,2.5-1.1,2.5-2.5S9.3,3.6,7.9,3.6S5.5,4.7,5.5,6.1S6.6,8.5,7.9,8.5z"/>',
  },
  blocks: {
    marker: Marker,
    maps: Map,
  },
  fields: {
    geolocation: Geolocation,
  },
});
