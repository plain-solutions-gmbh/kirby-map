<template>
  <k-field
    class="k-geolocation-field"
    v-bind="$props"
    :label="label"
    :required="required"
    :disabled="disabled"
  >
    <template #options>
      <k-button :text="$t('search')" icon="search" @click="openSearch()" />
    </template>

    <k-dialog ref="search" size="medium" :submitbutton="false">
      <k-text-field
        ref="name"
        class="k-geolocation-search"
        type="text"
        theme="field"
        :label="$t('map.blocks.marker.location.name')"
        :value="search"
        icon="search"
        @input="searchLocation($event)"
      />

      <k-collection
        :items="dropdownOptions"
        :empty="{ icon: 'alert', text: $t(searchError, 1) }"
        @item="selectDropdown($event)"
      />
    </k-dialog>

    <k-input
      ref="name"
      type="text"
      theme="field"
      :value="location.name"
      :before="$t('maps.field.geolocation.name')"
      @input="setValue($event, 'name')"
    />

    <k-input
      ref="lat"
      type="text"
      theme="field"
      :value="location.lat"
      :before="$t('maps.field.geolocation.lat')"
      @input="setValue($event, 'lat')"
    />

    <k-input
      ref="lng"
      type="text"
      theme="field"
      :value="location.lng"
      :before="$t('maps.field.geolocation.lng')"
      @input="setValue($event, 'lng')"
    />

    <k-box v-if="error" :text="error" theme="negative" />
  </k-field>
</template>

<script>
export default {
  props: {
    token: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    value: {
      type: Object,
      default() {
        return {
          name: undefined,
          lat: undefined,
          lng: undefined,
        };
      },
    },
    required: Boolean,
    disabled: Boolean,
    default: {
      type: Object,
      default() {
        return {
          name: "",
          lat: 0,
          lng: 0,
        };
      },
    },
  },

  data() {
    return {
      geoData: [],
      error: null,
      searchError: "search.min",
    };
  },

  computed: {
    mapId() {
      return `map-${this._uid}`;
    },

    location() {
      return {
        name: this.value?.name ?? this.default?.name ?? "",
        lat: this.value?.lat ?? this.default?.lat ?? 0,
        lng: this.value?.lng ?? this.default?.lng ?? 0,
      };
    },

    dropdownOptions() {
      return this.geoData.map(({ place_name, place_type, center }) => ({
        text: place_name,
        name: place_name,
        info: this.$t(`maps.field.geolocation.${place_type[0]}`),
        lat: center[1],
        lng: center[0],
      }));
    },
  },

  methods: {
    async searchLocation(evt) {
      if (evt.length === 0) {
        this.searchError = "maps.search.empty";
        return;
      }

      this.geoData = [];
      try {
        const response = await fetch(
          `https://api.mapbox.com/geocoding/v5/mapbox.places/${evt}.json?types=address,country,postcode,place,locality&limit=5&access_token=${this.token}`
        );
        const data = await response.json();
        this.searchError = "maps.field.geolocation.error.empty";

        if (data.features) {
          const toShow = data.features.slice(0, 8);

          if (toShow) {
            this.geoData = toShow;
          } else {
            this.searchError = "maps.search.error";
          }
        } else {
          this.searchError = data?.message;
        }
      } catch (err) {
        this.searchError = "maps.search.error";
      }
    },

    openSearch() {
      this.geoData = [];
      this.searchError = "maps.search.empty";
      this.$refs.search.open();
    },

    selectDropdown(selection) {
      console.log(selection);
      this.$refs.search.close();

      delete selection.type;
      delete selection.text;
      this.$refs.search.close();
      this.$emit("input", selection);
    },

    setValue(value, key) {
      let v = {
        name: key === "name" ? value : this.location.name,
        lat: key === "lat" ? value : this.location.lat,
        lng: key === "lng" ? value : this.location.lng,
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
    },
  },
};
</script>

<style>
.k-geolocation-search {
  margin-bottom: var(--spacing-4);
}
</style>
