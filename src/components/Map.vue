<template>
  <div class="embedded-maps">
    <div :id="mapId" class="embedded-map-item" @update="update"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      map: null,
      attachedMarker: [],
      attachedPopup: [],
      options: null,
    };
  },

  computed: {
    mapId() {
      return `map-${this._uid}`;
    },

    center() {
      if (!this.content.center) {
        return [0, 20];
      }

      const markerNode =
        this.field("marker").fieldsets.marker.tabs.content.fields.coordinates;
      const { name, lat, lng } = this.content.center;

      // Set default values for new markers
      markerNode.defaultName = name;
      markerNode.defaultLat = lat;
      markerNode.defaultLng = lng;

      return [lng, lat];
    },

    marker() {
      return this.content.marker.map(({ content }) => {
        const imgSize = content.image?.[0]?.info?.split(" × ") ?? [0, 0];

        return {
          image: content.image?.[0]?.url,
          width: (imgSize[0] / 100) * content.size,
          height: (imgSize[1] / 100) * content.size,
          lat: content.coordinates?.lat ?? content.coors?.lat ?? 0,
          lng: content.coordinates?.lng ?? content.coors?.lng ?? 51,
          anchor: content.anchor,
          hasPopup: content.haspopup,
          popup: content.popup,
          popupOffset: content.popupoffset,
        };
      });
    },
  },

  watch: {
    "content.style": function (value) {
      if (this.map) this.map.setStyle(`mapbox://styles/mapbox/${value}`);
    },

    "content.zoom": function (value) {
      if (this.map) this.map.setZoom(value);
    },

    center(value) {
      if (this.map) this.map.setCenter(value);
    },

    marker(value) {
      this.attachedPopup.forEach((marker) => marker.remove());
      this.attachedPopup = [];

      this.attachedMarker.forEach((marker) => marker.remove());
      this.attachedMarker = [];

      this.setMarker(value);
    },
  },

  created() {
    const cdnUrl = "https://api.mapbox.com/mapbox-gl-js/v2.3.1";
    let s;

    // Inject Mapbox library styles
    const cssLib = "mapbox-gl.css";
    if (!document.getElementById(cssLib)) {
      s = document.createElement("link");
      s.id = cssLib;
      s.href = `${cdnUrl}/${cssLib}`;
      s.rel = "stylesheet";
      document.head.appendChild(s);
    }

    // Inject Mapbox library core
    const jsLib = "mapbox-gl.js";
    if (!document.getElementById(jsLib)) {
      s = document.createElement("script");
      s.id = jsLib;
      s.addEventListener("load", this.initMap);
      s.src = `${cdnUrl}/${jsLib}`;
      document.body.appendChild(s);
    } else {
      this.initMap();
    }
  },

  methods: {
    async initMap() {
      const options = await this.$api.get("map/options");
      this.options = options;

      // eslint-disable-next-line no-undef
      mapboxgl.accessToken = this.options.token;
      // eslint-disable-next-line no-undef
      this.map = new mapboxgl.Map({
        container: this.mapId,
        center: this.center,
        style: `mapbox://styles/mapbox/${
          this.content.style ?? this.options.defaultStyle
        }`,
        zoom: this.content.zoom ?? 1,
      });

      this.map.scrollZoom.disable();
      this.setMarker(this.marker);
    },

    async setMarker(markerData) {
      if (!markerData) return;

      for (const marker of markerData) {
        let markerEl = null;

        if (marker.image) {
          markerEl = document.createElement("div");
          markerEl.className = "marker";
          markerEl.style.backgroundImage = `url(${marker.image})`;
          markerEl.style.width = `${marker.width}px`;
          markerEl.style.height = `${marker.height}px`;
          markerEl.style.backgroundSize = "100%";
        }

        // eslint-disable-next-line no-undef
        const curMarker = new mapboxgl.Marker({
          anchor: marker.anchor ?? null,
          element: markerEl,
        })
          .setLngLat([marker.lng, marker.lat])
          .addTo(this.map);

        this.attachedMarker.push(curMarker);

        if (marker.hasPopup) {
          // eslint-disable-next-line no-undef
          const curPopup = new mapboxgl.Popup({
            offset: parseInt(marker.popupOffset),
            focusAfterOpen: false,
          });

          curPopup.setLngLat([marker.lng, marker.lat]);
          curPopup.setHTML(marker.popup).addTo(this.map);
          this.attachedPopup.push(curPopup);
        }
      }
    },
  },
};
</script>

<style>
.embedded-maps {
  position: relative;
}

.embedded-map-item {
  min-height: 400px;
  overflow: hidden;
}
</style>
