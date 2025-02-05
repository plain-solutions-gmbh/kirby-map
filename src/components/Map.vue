<template>
  <div>
    <k-box theme="text" class="maps-bar">
      <k-button-group layout="collapsed">
        <k-button
          v-if="fail.length > 0"
          class="maps-token-fail"
          variant="filled"
          icon="alert"
          link="https://github.com/plain-solutions-gmbh/kirby-map"
          theme="negative"
          >{{ fail }}</k-button
        >
        <k-button
          v-if="!lock"
          :disabled="!newCenter"
          variant="filled"
          icon="circle-nested"
          @click="setNewCenter()"
          >{{ $t("maps.blocks.button.center") }}</k-button
        >
        <k-button
          v-if="!lock"
          variant="filled"
          :icon="wait ? 'loader' : 'pin'"
          :disabled="wait"
          @click="addMarker()"
          >{{ $t("maps.blocks.button.pin") }}</k-button
        >
        <k-button
          variant="filled"
          :theme="lock ? 'warning' : 'green'"
          :icon="lock ? 'lock' : 'unlock'"
          @click="toggleLock()"
        />
      </k-button-group>
    </k-box>

    <div class="embedded-maps">
      <div :id="mapId" class="embedded-map-item" @update="update"></div>
    </div>
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
      fail: "",
      lock: true,
      newCenter: false,
      wait: false,
    };
  },

  computed: {
    mapId() {
      return `map-${this._uid}`;
    },

    center() {
      if (!this.content.center || this.content.center.length < 1) {
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
    const cdnUrl = "https://api.mapbox.com/mapbox-gl-js/v3.9.4";
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

      if (options.token === null) {
        return (this.fail = this.$t("maps.error.token"));
      }

      const watchdog = setTimeout(() => {
        this.fail = this.$t("maps.error.timeout");
      }, 1000);

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

      this.toggleLock(this.content.center.length === 0);

      this.map.on("dragend", () => {
        this.newCenter = true;
      });

      this.map.on("zoom", () => {
        this.newCenter = true;
      });

      this.setMarker(this.marker);

      clearTimeout(watchdog);
    },

    async setMarker(markerData) {
      if (!markerData) return;

      for (const [i, marker] of markerData.entries()) {
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
          draggable: true,
        })
          .setLngLat([marker.lng, marker.lat])
          .addTo(this.map);

        curMarker.getElement().addEventListener("click", () => {
          const _this = this;
          this.$panel.drawer.open({
            component: "k-form-drawer",
            props: this.$helper.object.merge(
              this.field("marker").fieldsets.marker,
              {
                value: this.content.marker[i].content,
              }
            ),
            on: {
              input(value) {
                _this.content.marker[i].content = value;
                _this.$emit("update", this.content);
              },
            },
          });
        });

        curMarker.on("dragend", () => {
          const latlng = curMarker.getLngLat().wrap();
          this.content.marker[i].content.coordinates.lat = latlng.lat;
          this.content.marker[i].content.coordinates.lng = latlng.lng;
          this.$emit("update", this.content);
        });

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
    setNewCenter() {
      this.content.center = this.map.getCenter().wrap();
      this.content.zoom = this.map.getZoom();
      this.$emit("update", this.content);
      this.newCenter = false;
    },
    toggleLock(state = null) {
      state ??= this.lock;
      // eslint-disable-next-line no-cond-assign
      if ((this.lock = !state)) {
        this.map.scrollZoom.disable();
      } else {
        this.map.scrollZoom.enable();
      }
    },
    async addMarker() {
      this.wait = true;
      const newMarker = await this.$api.get(
        this.field("marker").endpoints.field + "/fieldsets/marker"
      );
      this.wait = false;
      newMarker.content.coordinates = this.map.getCenter().wrap();
      this.content.marker.push(newMarker);

      this.setMarker();

      this.$emit("update", this.content);
    },
  },
};
</script>

<style>
.embedded-maps {
  position: relative;
}

.maps-bar {
  position: absolute;
  width: auto;
  padding: var(--spacing-2) !important;
  border: 1px solid;
  right: var(--spacing-5);
  top: var(--spacing-5);
  z-index: 100;
}

.embedded-map-item {
  min-height: 400px;
  overflow: hidden;
}
</style>
