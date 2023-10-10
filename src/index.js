import Maps from "./components/Map.vue";
import Marker from "./components/Marker.vue";
import Geolocation from "./components/Geolocation.vue";

window.panel.plugin("microman/map", {
  blocks: {
    marker: Marker,
    maps: Maps,
  },
  fields: {
    geolocation: Geolocation,
  },
});
