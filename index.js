(function() {
  "use strict";
  const Map_vue_vue_type_style_index_0_lang = "";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main$2 = {
    data() {
      return {
        map: null,
        attachedMarker: [],
        attachedPopup: [],
        options: null
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
        const markerNode = this.field("marker").fieldsets.marker.tabs.content.fields.coordinates;
        const { name, lat, lng } = this.content.center;
        markerNode.defaultName = name;
        markerNode.defaultLat = lat;
        markerNode.defaultLng = lng;
        return [lng, lat];
      },
      marker() {
        return this.content.marker.map(({ content }) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n;
          const imgSize = (_d = (_c = (_b = (_a = content.image) == null ? void 0 : _a[0]) == null ? void 0 : _b.info) == null ? void 0 : _c.split(" \xD7 ")) != null ? _d : [0, 0];
          return {
            image: (_f = (_e = content.image) == null ? void 0 : _e[0]) == null ? void 0 : _f.url,
            width: imgSize[0] / 100 * content.size,
            height: imgSize[1] / 100 * content.size,
            lat: (_j = (_i = (_g = content.coordinates) == null ? void 0 : _g.lat) != null ? _i : (_h = content.coors) == null ? void 0 : _h.lat) != null ? _j : 0,
            lng: (_n = (_m = (_k = content.coordinates) == null ? void 0 : _k.lng) != null ? _m : (_l = content.coors) == null ? void 0 : _l.lng) != null ? _n : 51,
            anchor: content.anchor,
            hasPopup: content.haspopup,
            popup: content.popup,
            popupOffset: content.popupoffset
          };
        });
      }
    },
    watch: {
      "content.style": function(value) {
        if (this.map)
          this.map.setStyle(`mapbox://styles/mapbox/${value}`);
      },
      "content.zoom": function(value) {
        if (this.map)
          this.map.setZoom(value);
      },
      center(value) {
        if (this.map)
          this.map.setCenter(value);
      },
      marker(value) {
        this.attachedPopup.forEach((marker) => marker.remove());
        this.attachedPopup = [];
        this.attachedMarker.forEach((marker) => marker.remove());
        this.attachedMarker = [];
        this.setMarker(value);
      }
    },
    created() {
      const cdnUrl = "https://api.mapbox.com/mapbox-gl-js/v2.3.1";
      let s;
      const cssLib = "mapbox-gl.css";
      if (!document.getElementById(cssLib)) {
        s = document.createElement("link");
        s.id = cssLib;
        s.href = `${cdnUrl}/${cssLib}`;
        s.rel = "stylesheet";
        document.head.appendChild(s);
      }
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
        var _a, _b;
        const options = await this.$api.get("map/options");
        this.options = options;
        mapboxgl.accessToken = this.options.token;
        this.map = new mapboxgl.Map({
          container: this.mapId,
          center: this.center,
          style: `mapbox://styles/mapbox/${(_a = this.content.style) != null ? _a : this.options.defaultStyle}`,
          zoom: (_b = this.content.zoom) != null ? _b : 1
        });
        this.map.scrollZoom.disable();
        this.setMarker(this.marker);
      },
      async setMarker(markerData) {
        var _a;
        if (!markerData)
          return;
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
          const curMarker = new mapboxgl.Marker({
            anchor: (_a = marker.anchor) != null ? _a : null,
            element: markerEl
          }).setLngLat([marker.lng, marker.lat]).addTo(this.map);
          this.attachedMarker.push(curMarker);
          if (marker.hasPopup) {
            const curPopup = new mapboxgl.Popup({
              offset: parseInt(marker.popupOffset),
              focusAfterOpen: false
            });
            curPopup.setLngLat([marker.lng, marker.lat]);
            curPopup.setHTML(marker.popup).addTo(this.map);
            this.attachedPopup.push(curPopup);
          }
        }
      }
    }
  };
  var _sfc_render$2 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "embedded-maps" }, [_c("div", { staticClass: "embedded-map-item", attrs: { "id": _vm.mapId }, on: { "update": _vm.update } })]);
  };
  var _sfc_staticRenderFns$2 = [];
  _sfc_render$2._withStripped = true;
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$2,
    _sfc_render$2,
    _sfc_staticRenderFns$2,
    false,
    null,
    null,
    null,
    null
  );
  __component__$2.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-map/src/components/Map.vue";
  const Maps = __component__$2.exports;
  const Marker_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1 = {
    computed: {
      url() {
        var _a, _b, _c;
        return (_c = (_b = (_a = this.content.image) == null ? void 0 : _a[0]) == null ? void 0 : _b.icon) == null ? void 0 : _c.url;
      },
      description() {
        var _a, _b, _c;
        return (_c = (_a = this.content.coordinates) == null ? void 0 : _a.name) != null ? _c : (_b = this.content.coors) == null ? void 0 : _b.name;
      },
      coordinates() {
        return this.content.coordinates ? `${this.content.coordinates.lat},${this.content.coordinates.lng}` : this.content.coors ? "(This marker was created with an older version. Please set the position from scratch.)" : null;
      }
    }
  };
  var _sfc_render$1 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "marker-item", on: { "click": _vm.open } }, [_vm.url ? _c("div", { staticClass: "marker-item-image" }, [_c("img", { attrs: { "src": _vm.url } })]) : _vm._e(), _c("div", { staticClass: "marker-item-description" }, [_c("p", [_vm._v(_vm._s(_vm.description || _vm.$t("empty")))]), _c("p", [_vm._v(_vm._s(_vm.coordinates))])])]);
  };
  var _sfc_staticRenderFns$1 = [];
  _sfc_render$1._withStripped = true;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null,
    null,
    null
  );
  __component__$1.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-map/src/components/Marker.vue";
  const Marker = __component__$1.exports;
  const Geolocation_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    props: {
      token: {
        type: String,
        required: true
      },
      label: {
        type: String,
        required: true
      },
      value: {
        type: Object,
        default() {
          return {
            name: void 0,
            lat: void 0,
            lng: void 0
          };
        }
      },
      required: Boolean,
      disabled: Boolean,
      default: {
        type: Object,
        default() {
          return {
            name: "",
            lat: 0,
            lng: 0
          };
        }
      }
    },
    data() {
      return {
        geoData: [],
        error: null
      };
    },
    computed: {
      mapId() {
        return `map-${this._uid}`;
      },
      location() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l;
        return {
          name: (_d = (_c = (_a = this.value) == null ? void 0 : _a.name) != null ? _c : (_b = this.default) == null ? void 0 : _b.name) != null ? _d : "",
          lat: (_h = (_g = (_e = this.value) == null ? void 0 : _e.lat) != null ? _g : (_f = this.default) == null ? void 0 : _f.lat) != null ? _h : 0,
          lng: (_l = (_k = (_i = this.value) == null ? void 0 : _i.lng) != null ? _k : (_j = this.default) == null ? void 0 : _j.lng) != null ? _l : 0
        };
      },
      dropdownOptions() {
        return this.geoData.map(({ place_name, place_type, center }) => ({
          name: place_name,
          type: place_type[0],
          lat: center[1],
          lng: center[0]
        }));
      }
    },
    methods: {
      async searchLocation(evt) {
        if (!evt) {
          this.$refs.dropdown.close();
          return;
        }
        try {
          const response = await fetch(
            `https://api.mapbox.com/geocoding/v5/mapbox.places/${evt}.json?types=address,country,postcode,place,locality&limit=5&access_token=${this.token}`
          );
          const data = await response.json();
          this.error = null;
          if (data.features) {
            const toShow = data.features.slice(0, 6);
            if (toShow) {
              this.geoData = toShow;
              this.$refs.dropdown.open();
            } else {
              this.error = this.$t("maps.field.L.error.empty");
              this.$refs.dropdown.close();
            }
          } else {
            this.error = data.message;
            this.$refs.dropdown.close();
          }
        } catch (err) {
          this.$refs.dropdown.close();
        }
      },
      selectDropdown(selection) {
        delete selection.type;
        this.$refs.dropdown.close();
        this.$emit("input", selection);
      },
      setValue(value, key) {
        let v = {
          name: key === "name" ? value : this.location.name,
          lat: key === "lat" ? value : this.location.lat,
          lng: key === "lng" ? value : this.location.lng
        };
        this.$emit("input", v);
        if (this.required && (!v.name || !v.lat || !v.lng)) {
          this.error = this.$t("maps.field.geolocation.error");
          return;
        }
        if (!v.name) {
          this.error = this.$t("maps.field.geolocation.name.error");
          return;
        }
        if (!v.lat || isNaN(v.lat) || Math.abs(v.lat) > 90) {
          this.error = this.$t("maps.field.geolocation.lat.error");
          return;
        }
        if (!v.lng || isNaN(v.lng) || Math.abs(v.lng) > 180) {
          this.error = this.$t("maps.field.geolocation.lng.error");
          return;
        }
        this.error = null;
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-field", { staticClass: "k-geolocation-field", attrs: { "label": _vm.label, "required": _vm.required, "disabled": _vm.disabled } }, [_c("k-text-field", { ref: "name", staticClass: "k-geolocation-search", attrs: { "type": "text", "theme": "field", "value": _vm.search, "icon": "search" }, on: { "input": _vm.searchLocation } }), _c("k-dropdown", [_c("k-dropdown-content", { ref: "dropdown" }, _vm._l(_vm.dropdownOptions, function(option, index) {
      return _c("k-dropdown-item", { key: index, on: { "click": function($event) {
        return _vm.selectDropdown(option);
      } }, nativeOn: { "keydown": [function($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "enter", 13, $event.key, "Enter"))
          return null;
        $event.preventDefault();
        return _vm.selectDropdown(option);
      }, function($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "space", 32, $event.key, [" ", "Spacebar"]))
          return null;
        $event.preventDefault();
        return _vm.selectDropdown(option);
      }] } }, [_c("span", { domProps: { "innerHTML": _vm._s(
        _vm.$t(`maps.field.geolocation.${option.type}`) + ": " + option.name
      ) } })]);
    }), 1)], 1), _c("k-input", { ref: "name", attrs: { "type": "text", "theme": "field", "value": _vm.location.name, "before": _vm.$t("maps.field.geolocation.name") }, on: { "input": function($event) {
      return _vm.setValue($event, "name");
    } } }), _c("k-input", { ref: "lat", attrs: { "type": "text", "theme": "field", "value": _vm.location.lat, "before": _vm.$t("maps.field.geolocation.lat") }, on: { "input": function($event) {
      return _vm.setValue($event, "lat");
    } } }), _c("k-input", { ref: "lng", attrs: { "type": "text", "theme": "field", "value": _vm.location.lng, "before": _vm.$t("maps.field.geolocation.lng") }, on: { "input": function($event) {
      return _vm.setValue($event, "lng");
    } } }), _vm.error ? _c("k-box", { attrs: { "text": _vm.error, "theme": "negative" } }) : _vm._e()], 1);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/romangsponer/Cloud/_sites/plugin-env/site/plugins/kirby-map/src/components/Geolocation.vue";
  const Geolocation = __component__.exports;
  window.panel.plugin("microman/map", {
    blocks: {
      marker: Marker,
      maps: Maps
    },
    fields: {
      geolocation: Geolocation
    }
  });
})();
