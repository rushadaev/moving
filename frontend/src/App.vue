<script setup lang="ts">
import { computed } from 'vue'
import { RouterView } from 'vue-router'
import { NConfigProvider, NMessageProvider, darkTheme, type GlobalThemeOverrides } from 'naive-ui'
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";

// Check if user prefers dark mode
const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches

/**
 * @type import('naive-ui').GlobalThemeOverrides
 */
const lightThemeOverrides: GlobalThemeOverrides = {
  common: {
    primaryColor: '#5D87EE',
    primaryColorHover: '#3D5FBB',
    primaryColorPressed: '#2D4F9B',
    primaryColorSuppl: '#5D87EE',
    
    infoColor: '#5D87EE',
    infoColorHover: '#3D5FBB',
    infoColorPressed: '#2D4F9B',
    infoColorSuppl: '#5D87EE',
    
    successColor: '#52C41A',
    warningColor: '#FAAD14',
    errorColor: '#F97066',
    
    textColorBase: '#222222',
    textColor1: '#222222',
    textColor2: '#666666',
    textColor3: '#999999',
    
    baseColor: '#FFFFFF',
    bodyColor: '#E6E6E8',
    cardColor: '#F5F5F5',
    modalColor: '#FFFFFF',
    popoverColor: '#FFFFFF',
    
    borderColor: 'rgb(224, 224, 224)',
    dividerColor: 'rgb(224, 224, 224)',
    inputColor: 'rgb(245, 245, 245)',
    inputColorDisabled: 'rgb(250, 250, 250)',
    
    borderRadius: '8px',
    fontFamily: 'Montserrat, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
    fontSize: '16px',
  },
  Card: {
    color: '#FFFFFF',
    textColor: '#222222',
    titleTextColor: '#222222',
    borderColor: 'transparent',
    actionColor: '#FFFFFF',
    headerTextColor: '#222222',
    headerFontWeight: '600',
    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.08)',
  },
  Modal: {
    color: '#FFFFFF',
    textColor: '#222222',
    titleTextColor: '#222222',
    borderColor: 'rgb(224, 224, 224)',
    headerTextColor: '#222222',
    bodyPadding: '12px 16px',
    headerPadding: '12px 16px',
    footerPadding: '12px 16px',
  },
  Dialog: {
    color: '#FFFFFF',
    textColor: '#222222',
    titleTextColor: '#222222',
  },
  Button: {
    textColor: '#222222',
    textColorHover: '#222222',
    textColorPressed: '#222222',
    textColorFocus: '#222222',
    textColorDisabled: '#999999',
    textColorPrimary: '#FFFFFF',
    textColorHoverPrimary: '#FFFFFF',
    textColorPressedPrimary: '#FFFFFF',
    textColorFocusPrimary: '#FFFFFF',
    textColorDisabledPrimary: '#FFFFFF',
    colorDisabled: 'rgb(250, 250, 250)',
    borderDisabled: '1px solid rgb(224, 224, 224)',
  },
  Input: {
    color: 'rgb(245, 245, 245)',
    colorFocus: 'rgb(245, 245, 245)',
    colorDisabled: 'rgb(250, 250, 250)',
    textColor: '#222222',
    placeholderColor: '#999999',
    placeholderColorDisabled: '#CCCCCC',
    border: '1px solid rgb(224, 224, 224)',
    borderHover: '1px solid #5D87EE',
    borderFocus: '1px solid #5D87EE',
    borderDisabled: '1px solid rgb(224, 224, 224)',
    caretColor: '#5D87EE',
    clearColor: '#666666',
    clearColorHover: '#333333',
    clearColorPressed: '#333333',
  },
  InputNumber: {
    color: 'rgb(245, 245, 245)',
    colorFocus: 'rgb(245, 245, 245)',
    colorDisabled: 'rgb(250, 250, 250)',
    textColor: '#222222',
    placeholderColor: '#999999',
    placeholderColorDisabled: '#CCCCCC',
    border: '1px solid rgb(224, 224, 224)',
    borderHover: '1px solid #5D87EE',
    borderFocus: '1px solid #5D87EE',
    borderDisabled: '1px solid rgb(224, 224, 224)',
    caretColor: '#5D87EE',
  },
  Select: {
    peers: {
      InternalSelection: {
        textColor: '#222222',
        textColorDisabled: '#999999',
        placeholderColor: '#999999',
        placeholderColorDisabled: '#CCCCCC',
        color: 'rgb(245, 245, 245)',
        colorDisabled: 'rgb(250, 250, 250)',
        colorActive: 'rgb(245, 245, 245)',
        border: '1px solid rgb(224, 224, 224)',
        borderHover: '1px solid #5D87EE',
        borderFocus: '1px solid #5D87EE',
        borderActive: '1px solid #5D87EE',
        borderDisabled: '1px solid rgb(224, 224, 224)',
        arrowColor: '#666666',
        arrowColorDisabled: '#CCCCCC',
      },
      InternalSelectMenu: {
        color: '#FFFFFF',
        colorHover: '#F5F5F5',
        colorActive: '#E6E6E8',
        colorPending: '#F5F5F5',
        optionTextColor: '#222222',
        optionTextColorHover: '#222222',
        optionTextColorActive: '#222222',
        optionTextColorDisabled: '#999999',
        optionCheckColor: '#5D87EE',
      }
    }
  },
  DatePicker: {
    itemTextColor: '#222222',
    itemTextColorHover: '#222222',
    itemTextColorPressed: '#FFFFFF',
    itemTextColorActive: '#FFFFFF',
    itemColorHover: '#F5F5F5',
    itemColorPressed: '#5D87EE',
    itemColorActive: '#5D87EE',
    panelColor: '#FFFFFF',
    panelTextColor: '#222222',
    panelHeaderDividerColor: 'rgb(224, 224, 224)',
  },
  Message: {
    color: '#FFFFFF',
    textColor: '#222222',
    iconColor: '#5D87EE',
    iconColorInfo: '#5D87EE',
    iconColorSuccess: '#52C41A',
    iconColorWarning: '#FAAD14',
    iconColorError: '#F97066',
  },
}

/**
 * @type import('naive-ui').GlobalThemeOverrides
 */
const darkThemeOverrides: GlobalThemeOverrides = {
  common: {
    primaryColor: '#5D87EE',
    primaryColorHover: '#7DA3F0',
    primaryColorPressed: '#3D5FBB',
    primaryColorSuppl: '#5D87EE',
    
    infoColor: '#5D87EE',
    infoColorHover: '#7DA3F0',
    infoColorPressed: '#3D5FBB',
    infoColorSuppl: '#5D87EE',
    
    successColor: '#52C41A',
    warningColor: '#FAAD14',
    errorColor: '#F97066',
    
    textColorBase: '#FFFFFF',
    textColor1: '#FFFFFF',
    textColor2: '#CCCCCC',
    textColor3: '#999999',
    
    baseColor: '#000000',
    bodyColor: '#1A1A1A',
    cardColor: '#2A2A2A',
    modalColor: '#2A2A2A',
    popoverColor: '#2A2A2A',
    
    borderColor: 'rgb(51, 51, 51)',
    dividerColor: 'rgb(51, 51, 51)',
    inputColor: 'rgb(42, 42, 42)',
    inputColorDisabled: 'rgb(38, 38, 38)',
    
    borderRadius: '8px',
    fontFamily: 'Montserrat, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
    fontSize: '16px',
  },
  Card: {
    color: '#2A2A2A',
    textColor: '#FFFFFF',
    titleTextColor: '#FFFFFF',
    borderColor: 'transparent',
    actionColor: '#2A2A2A',
    headerTextColor: '#FFFFFF',
    headerFontWeight: '600',
    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.3)',
  },
  Modal: {
    color: '#2A2A2A',
    textColor: '#FFFFFF',
    titleTextColor: '#FFFFFF',
    borderColor: 'rgb(51, 51, 51)',
    headerTextColor: '#FFFFFF',
    bodyPadding: '12px 16px',
    headerPadding: '12px 16px',
    footerPadding: '12px 16px',
  },
  Dialog: {
    color: '#2A2A2A',
    textColor: '#FFFFFF',
    titleTextColor: '#FFFFFF',
  },
  Message: {
    color: '#2A2A2A',
    textColor: '#FFFFFF',
    iconColor: '#5D87EE',
    iconColorInfo: '#5D87EE',
    iconColorSuccess: '#52C41A',
    iconColorWarning: '#FAAD14',
    iconColorError: '#F97066',
  },
  Input: {
    color: 'rgb(42, 42, 42)',
    colorFocus: 'rgb(42, 42, 42)',
    colorDisabled: 'rgb(38, 38, 38)',
    textColor: '#FFFFFF',
    placeholderColor: '#999999',
    placeholderColorDisabled: '#666666',
    border: '1px solid rgb(51, 51, 51)',
    borderHover: '1px solid #7DA3F0',
    borderFocus: '1px solid #5D87EE',
    borderDisabled: '1px solid rgb(51, 51, 51)',
    caretColor: '#5D87EE',
    clearColor: '#999999',
    clearColorHover: '#CCCCCC',
    clearColorPressed: '#CCCCCC',
  },
  InputNumber: {
    color: 'rgb(42, 42, 42)',
    colorFocus: 'rgb(42, 42, 42)',
    colorDisabled: 'rgb(38, 38, 38)',
    textColor: '#FFFFFF',
    placeholderColor: '#999999',
    placeholderColorDisabled: '#666666',
    border: '1px solid rgb(51, 51, 51)',
    borderHover: '1px solid #7DA3F0',
    borderFocus: '1px solid #5D87EE',
    borderDisabled: '1px solid rgb(51, 51, 51)',
    caretColor: '#5D87EE',
  },
  Select: {
    peers: {
      InternalSelection: {
        textColor: '#FFFFFF',
        textColorDisabled: '#666666',
        placeholderColor: '#999999',
        placeholderColorDisabled: '#666666',
        color: 'rgb(42, 42, 42)',
        colorDisabled: 'rgb(38, 38, 38)',
        colorActive: 'rgb(42, 42, 42)',
        border: '1px solid rgb(51, 51, 51)',
        borderHover: '1px solid #7DA3F0',
        borderFocus: '1px solid #5D87EE',
        borderActive: '1px solid #5D87EE',
        borderDisabled: '1px solid rgb(51, 51, 51)',
        arrowColor: '#999999',
        arrowColorDisabled: '#666666',
      },
      InternalSelectMenu: {
        color: '#2A2A2A',
        colorHover: '#3A3A3A',
        colorActive: '#333333',
        colorPending: '#3A3A3A',
        optionTextColor: '#FFFFFF',
        optionTextColorHover: '#FFFFFF',
        optionTextColorActive: '#FFFFFF',
        optionTextColorDisabled: '#666666',
        optionCheckColor: '#5D87EE',
      }
    }
  },
  DatePicker: {
    itemTextColor: '#FFFFFF',
    itemTextColorHover: '#FFFFFF',
    itemTextColorPressed: '#FFFFFF',
    itemTextColorActive: '#FFFFFF',
    itemColorHover: '#3A3A3A',
    itemColorPressed: '#5D87EE',
    itemColorActive: '#5D87EE',
    panelColor: '#2A2A2A',
    panelTextColor: '#FFFFFF',
    panelHeaderDividerColor: 'rgb(51, 51, 51)',
  },
  Checkbox: {
    textColor: '#FFFFFF',
    colorChecked: '#5D87EE',
    colorDisabled: 'rgb(38, 38, 38)',
    colorDisabledChecked: '#3D5FBB',
    border: '1px solid rgb(51, 51, 51)',
    borderDisabled: '1px solid rgb(51, 51, 51)',
    borderChecked: '1px solid #5D87EE',
    borderFocus: '1px solid #5D87EE',
    boxShadowFocus: '0 0 0 2px rgba(93, 135, 238, 0.2)',
  },
}

const theme = computed(() => prefersDark ? darkTheme : null)
const themeOverrides = computed(() => prefersDark ? darkThemeOverrides : lightThemeOverrides)
</script>

<template>
  <n-config-provider :theme="theme" :theme-overrides="themeOverrides">
    <n-message-provider>
      <div class="main-content">
        <RouterView />
      </div>
      <Footer/>
    </n-message-provider>
  </n-config-provider>
</template>

<style scoped>
.main-content {
  padding-top: 55px; /* Height of header */
  padding-bottom: 70px; /* Height of footer */
  min-height: 100vh;
  background: var(--color-background);
}
</style>