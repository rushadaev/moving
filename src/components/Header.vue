<template>
  <header :title="title">
    <div
        class="mobile-top">
      <LeftButton v-show="isLeftButton"/>
      <div class="wrapper">
        {{ title }}
      </div>
      <RightButton v-show="isRightButton"/>
    </div>
  </header>
</template>
<script setup lang="ts">
withDefaults(defineProps<{
  title: string,
  isLeftButton?: boolean,
  isRightButton?: boolean
}>(), {
  title: "Move Easy",
  isLeftButton: true,
  isRightButton: true,
})
import {NText} from "naive-ui";
import LeftButton from "@/components/ui/Buttons/LeftButton.vue";
import RightButton from "@/components/ui/Buttons/RightButton.vue";



</script>

<style scoped>
header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  padding: 2px 5px;
  z-index: 100;
}
.mobile-top {
  display: grid;
  grid-template-columns: 1fr auto 1fr; /* Allocate space for 3 columns */
  align-items: center; /* Vertically align items in the center */
  text-align: center; /* Center-align the content inside grid items */
  width: 100%;
  padding: 5px 4px;
  height: 50px;
  background: var(--color-background-mute);
  color: var(--color-text);
  border-radius: 10px;
  margin: 0 auto;
}

.mobile-top > .wrapper {
  grid-column: 2; /* Center the wrapper in the middle column */
  justify-self: center; /* Center-align the wrapper */
}

.mobile-top > :first-child {
  justify-self: start; /* Align the first button to the start */
}

.mobile-top > :last-child {
  justify-self: end; /* Align the last button to the end */
}
</style>