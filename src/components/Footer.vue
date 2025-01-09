<template>
  <footer>
    <div class="mobile-navigation">
      <n-config-provider :theme-overrides="themeOverrides">
        <n-button-group>
          <RouterLink to="/">
            <n-button :type="isActive('/') ? 'primary' : 'default'" size="large">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconRequest/>
                <span class="menu-label">Request</span>
              </div>
            </n-button>
          </RouterLink>
          <RouterLink to="/details">
            <n-button :type="isActive('/details') ? 'primary' : 'default'" size="large">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconDetails/>
                <span class="menu-label">Details</span>
              </div>
            </n-button>
          </RouterLink>
          <RouterLink to="/tracking">
            <n-button :type="isActive('/tracking') ? 'primary' : 'default'" size="large">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconTracking/>
                <span class="menu-label">Tracking</span>
              </div>
            </n-button>
          </RouterLink>
        </n-button-group>
      </n-config-provider>
    </div>
  </footer>
</template>
<script setup lang="ts">
import { useRoute } from "vue-router";
import {NButton, NButtonGroup, NConfigProvider, NIcon} from "naive-ui";
import IconDetails from "@/components/icons/mobile-navigation/IconDetails.vue";
import IconRequest from "@/components/icons/mobile-navigation/IconRequest.vue";
import IconTracking from "@/components/icons/mobile-navigation/IconTracking.vue";

const route = useRoute();

const isActive = (path: string) => route.path === path

const themeOverrides = {
  Button: {
    heightLarge: '70px', // Custom height for large buttons
    borderRadius: '10px', // Optional: Custom border radius
    fontSizeLarge: '12px', // Optional: Adjust font size

    textColor: '#222222',

    // Background styles
    color: '#FFFFFF', // Default background color
    colorHover: '#5D87EE10', // Background color on hover
    colorPressed: '#3D5FBB10', // Background color when pressed
    colorFocus: '#5D87EE10', // Background color when focused

    colorPrimary: '#5D87EE20',
    textColorPrimary: '#5D87EE',
    borderPrimary: 'none',
    colorHoverPrimary: '#5D87EE20',
    colorPressedPrimary: '#5D87EE20',
    colorFocusPrimary: '#5D87EE20',
    borderFocusPrimary: 'none',
    borderPressedPrimary: 'none',
    textColorPressedPrimary: '#5D87EE50',
    textColorFocusPrimary: '#5D87EE',

    fontWeight: 'bold',

    textColorHoverPrimary: '#5D87EE',
    borderHoverPrimary: 'none',

    // Hover styles
    textColorHover: '#5D87EE', // Text color on hover

    // Pressed state
    textColorPressed: '#5D87EE', // Text color when pressed

    // Focus state
    textColorFocus: '#5D87EE', // Text color when focused

    // Border styles
    border: 'none', // No border by default
    borderHover: 'none',
    borderPressed: 'none',
    borderFocus: 'none',
  },
};
</script>

<style scoped>
footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  padding-bottom: 5px;
  z-index: 100;
}
.mobile-navigation {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  background: var(--vt-c-white);
  border-radius: 10px;
  overflow: hidden;
  margin: 0 auto;
  width: max-content;
}
.menu-label{
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 500;
  font-size: 12px;
  line-height: 15px;

}
</style>
