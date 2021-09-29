<template>
  <k-field
    class="k-geolocation-field"
    :label="label"
    :required="required"
    :disabled="disabled"
  >
    <k-text-field
      class="k-geolocation-search"
      type="text"
      theme="field"
      ref="name"
      :value="this['search']"
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
              $t('maps.field.geolocation.' + option.type) + ': ' + option.name
            "
          />
        </k-dropdown-item>
      </k-dropdown-content>
    </k-dropdown>

    <k-input
      type="text"
      theme="field"
      ref="name"
      :value="this.value.name"
      :before="$t('maps.field.geolocation.name')"
      @input="setValue($event, 'name')"
    />

    <k-input
      type="text"
      theme="field"
      ref="lat"
      :value="this.value.lat"
      :before="$t('maps.field.geolocation.lat')"
      @input="setValue($event, 'lat')"
    />

    <k-input
      type="text"
      theme="field"
      ref="lng"
      :value="this.value.lng"
      :before="$t('maps.field.geolocation.lng')"
      @input="setValue($event, 'lng')"
    />

    <k-box v-if="error" :text="$t(error)" theme="negative" />
  </k-field>
</template>

<script>
export default {
  data() {
    return {
      geodata: [],
      geolocation: "",
      error: "",
    };
  },

  computed: {
    mapId() {
      return "map-" + this._uid;
    },

    value() {
      if (this._props.value === null) {
        if (this.default === undefined)
          this.default = { name: undefined, lat: undefined, lng: undefined };

        return {
          name: this.default.name || "",
          lat: this.default.lat || 0,
          lng: this.default.lng || 0,
        };
      }
      return this._props.value;
    },

    dropdownOptions() {
      return this.geodata.map((el) => {
        return {
          name: el.place_name,
          type: el.place_type[0],
          lat: el.center[1],
          lng: el.center[0],
        };
      });
    },
  },
  props: {
    token: String,
    label: String,
    value: String,
    required: Boolean,
    disabled: Boolean,
    default: Object,
  },
  mounted() {
    if (this.default === null)
      this.default = { name: undefined, lat: undefined, lng: undefined };
  },
  methods: {
    searchLocation(e) {
      if (e.length > 0) {
        fetch(
          "https://api.mapbox.com/geocoding/v5/mapbox.places/" +
            e +
            ".json?types=address,country,postcode,place,locality&limit=5&access_token=" +
            this.token
        )
          .then((response) => response.json())
          .then((response) => {
            this.error = "";
            if (response.features !== undefined) {
              let toShow = response.features.slice(0, 6);
              if (toShow.length > 0) {
                this.geodata = toShow;
                this.$refs.dropdown.open();
              } else {
                this.error = this.$t("maps.field.geolocation.error.empty");
                this.$refs.dropdown.close();
              }
            } else {
              this.error = response.message;
              this.$refs.dropdown.close();
            }
          })
          .catch((error) => {
            this.$refs.dropdown.close();
          });
      } else {
        this.$refs.dropdown.close();
      }
    },

    selectDropdown(selection) {
      delete selection.type;
      this.$refs.dropdown.close();
      this.$emit("input", selection);
    },

    setValue(value, key, writeMsg = true) {
      this.error = "";

      let v = {
        name: key == "name" ? value : this.value.name,
        lat: key == "lat" ? value : this.value.lat,
        lng: key == "lng" ? value : this.value.lng,
      };

      if (
        this.required &&
        (v.name.length == 0 || v.lat.length == 0 || v.lng.length == 0)
      ) {
        this.error = writeMsg ? this.$t("maps.field.geolocation.error") : "";
        return false;
      }

      if (v.name !== undefined && v.name.length == 0) {
        this.error = writeMsg
          ? this.$t("maps.field.geolocation.lat.error")
          : "";
        return false;
      }

      if (v.lat === undefined || isNaN(v.lat) || Math.abs(v.lat) > 90) {
        this.error = writeMsg
          ? this.$t("maps.field.geolocation.lat.error")
          : "";
        return false;
      }

      if (v.lng === undefined || isNaN(v.lng) || Math.abs(v.lng) > 180) {
        this.error = writeMsg
          ? this.$t("maps.field.geolocation.lng.error")
          : "";
        return false;
      }

      this.$emit("input", v);
    },
  },
};
</script>

<style lang="scss">
.k-geolocation-search > header {
  display: none;
}
</style>
