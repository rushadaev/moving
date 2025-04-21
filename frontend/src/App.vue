<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import HelloWorld from './components/HelloWorld.vue'
import {NButton, NConfigProvider} from 'naive-ui'
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";

const themeOverrides = {
  common: {
    primaryColor: '#5D87EE',         // Main accent color
    primaryColorHover: '#3D5FBB',   // Hover color
    primaryColorPressed: '#3D5FBB', // Pressed color
    primaryColorSuppl: '#5D87EE',   // Supplementary primary color

    bodyColor: '#E6E6E8',           // Background color
    textColorBase: '#222222',       // Base text color
    textColor2: '#222222',          // Secondary text color
    cardColor: '#F5F5F5',           // Secondary background color
    modalColor: '#FFFFFF',          // White for modal backgrounds
  }
};
</script>

<template>
  <n-config-provider :theme-overrides="themeOverrides">
    <Header title="Header"/>
    <RouterView class="py-[75px]" />
    <Footer/>
  </n-config-provider>
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}


</style>