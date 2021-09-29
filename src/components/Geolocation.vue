<template>
  <k-field
    class="k-geolocation-field"
    :label="label"
    :required="required"
    :disabled="disabled"
  >
    <k-text-field
      ref="name"
      class="k-geolocation-search"
      type="text"
      theme="field"
      :value="search"
      icon="search"
      @input="searchLocation"
    />

    <k-dropdown>
      <k-dropdown-content ref="dropdown">
        <k-dropdown-item
          v-for="(option, index) in dropdownOptions"
          :key="index"
          @click="selectDropdown(option)"
          @keydown.native.enter.prevent="selectDropdown(option)"
          @keydown.native.space.prevent="selectDropdown(option)"
        >
          <span
            v-html="
              $t(`maps.field.geolocation.${option.type}`) + ': ' + option.name
            "
          />
        </k-dropdown-item>
      </k-dropdown-content>
    </k-dropdown>

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

    <k-box v-if="error" :text="$t(error)" theme="negative" />
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
      error: "",
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
      return this.geoData.map((el) => {
        return {
          name: el.place_name,
          type: el.place_type[0],
          lat: el.center[1],
          lng: el.center[0],
        };
      });
    },
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
        this.error = "";

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
        lng: key === "lng" ? value : this.location.lng,
      };

      if (this.required && (!v.name || !v.lat || !v.lng)) {
        this.error = this.$t("maps.field.geolocation.error");
        return;
      }

      if (!v.name) {
        this.error = this.$t("maps.field.geolocation.lat.error");
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

      this.error = "";
      this.$emit("input", v);
    },
  },
};
</script>

<style>
.k-geolocation-search > header {
  display: none;
}
</style>
