<template>
  <footer>
    <div class="mobile-navigation">
      <n-config-provider :theme-overrides="themeOverrides">
        <n-button-group class="full-width-button-group">
          <RouterLink to="/requests" class="nav-link">
            <n-button :type="isActive('/requests') ? 'primary' : 'default'" size="large" class="nav-button">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconRequest/>
                <span class="menu-label">Requests</span>
              </div>
            </n-button>
          </RouterLink>
          <RouterLink v-if="hasSelectedRequest" to="/details" class="nav-link">
            <n-button :type="isActive('/details') ? 'primary' : 'default'" size="large" class="nav-button">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconDetails/>
                <span class="menu-label">Details</span>
              </div>
            </n-button>
          </RouterLink>
          <div v-else class="nav-link">
            <n-button disabled size="large" class="nav-button">
              <div class="flex flex-col justify-center items-center gap-1">
                <IconDetails/>
                <span class="menu-label">Details</span>
              </div>
            </n-button>
          </div>
          
          <template v-if="isActive('/tracking')">
            <RouterLink to="/route" class="nav-link">
              <n-button :type="isActive('/route') ? 'primary' : 'default'" size="large" class="nav-button">
                <div class="flex flex-col justify-center items-center gap-1">
                  <IconDetails/>
                  <span class="menu-label">Route</span>
                </div>
              </n-button>
            </RouterLink>
          </template>
          
          <template v-else>
            <RouterLink v-if="hasSelectedRequest" to="/tracking" class="nav-link">
              <n-button :type="isActive('/tracking') ? 'primary' : 'default'" size="large" class="nav-button">
                <div class="flex flex-col justify-center items-center gap-1">
                  <IconTracking/>
                  <span class="menu-label">Tracking</span>
                </div>
              </n-button>
            </RouterLink>
            <div v-else class="nav-link">
              <n-button disabled size="large" class="nav-button">
                <div class="flex flex-col justify-center items-center gap-1">
                  <IconTracking/>
                  <span class="menu-label">Tracking</span>
                </div>
              </n-button>
            </div>
          </template>
        </n-button-group>
      </n-config-provider>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { computed } from "vue";
import { useRequestsStore } from "@/stores/requests";
import {NButton, NButtonGroup, NConfigProvider, NIcon} from "naive-ui";
import IconDetails from "@/components/icons/mobile-navigation/IconDetails.vue";
import IconRequest from "@/components/icons/mobile-navigation/IconRequest.vue";
import IconTracking from "@/components/icons/mobile-navigation/IconTracking.vue";

const route = useRoute();
const requestsStore = useRequestsStore();

const isActive = (path: string) => route.path === path;
const hasSelectedRequest = computed(() => !!requestsStore.selectedRequest);

const themeOverrides = {
  Button: {
    heightLarge: '70px',
    borderRadius: '0px',
    fontSizeLarge: '12px',
    textColor: 'var(--color-text)',
    color: 'var(--color-background-soft)',
    colorHover: '#5D87EE10',
    colorPressed: '#3D5FBB10',
    colorFocus: '#5D87EE10',
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
    textColorHover: '#5D87EE',
    textColorPressed: '#5D87EE',
    textColorFocus: '#5D87EE',
    border: 'none',
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
  z-index: 100;
}

.mobile-navigation {
  width: 100%;
  background: var(--color-background-mute);
}

.full-width-button-group {
  width: 100%;
  display: flex;
}

.nav-link {
  flex: 1;
  display: block;
}

.nav-button {
  width: 100%;
}

.menu-label{
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 500;
  font-size: 12px;
  line-height: 15px;
  color: var(--color-text);
}
</style>
