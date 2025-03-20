import Maps from "@/components/Map.vue";
import Marker from "@/components/Marker.vue";
import Geolocation from "@/components/Geolocation.vue";
import SearchDialog from "@/components/SearchDialog.vue";
import PlainLicense from "../utils/PlainLicense.vue";

window.panel.plugin("plain/map", {
  blocks: {
    marker: Marker,
    maps: Maps,
  },
  fields: {
    geolocation: Geolocation,
  },
  components: {
    geolocationSearchDialog: SearchDialog,
    "k-plain-license": PlainLicense,
  },
});
