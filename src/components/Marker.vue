<template>
  <div class="marker-item" @click="open">
    <span class="img-wrapper">
      <img v-if="img" :src="img" />
    </span>

    <div class="desc-wrapper">
      <p>{{ name || $t("empty") }}</p>
      <p>{{ coors }}</p>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    img() {
      if (this.content.image.length > 0) return this.content.image[0].icon.url;
      this.content.anchor = null;
      return false;
    },
    name() {
      if (this.content.coors) return this.content.coors.name;

      return false;
    },
    coors() {
      if (this.content.coors)
        return this.content.coors.lat + "," + this.content.coors.lng;
      return "";
    },
  },
};
</script>

<style lang="scss">
.marker-item {
  display: flex;
  align-items: center;
  > .img-wrapper {
    height: 38px;
    width: 38px;
    img {
      height: 100%;
      width: 100%;
      object-fit: contain;
    }
  }
}
.desc-wrapper {
  padding-left: 15px;
  > p:last-child {
    color: #ccc;
    font-style: italic;
  }
}
</style>
