# Moving App Style Guide

## Design System Overview

This style guide documents the design patterns, components, and conventions used throughout the Moving App frontend.

## Table of Contents
1. [Color System](#color-system)
2. [Typography](#typography)
3. [Spacing & Layout](#spacing--layout)
4. [Components](#components)
5. [Forms](#forms)
6. [Buttons](#buttons)
7. [Modals](#modals)
8. [Dark Mode](#dark-mode)
9. [Code Conventions](#code-conventions)

## Color System

### Brand Colors
```css
--color-primary: #5D87EE;           /* Main brand color */
--color-primary-hover: #3D5FBB;     /* Hover state */
--color-primary-pressed: #2D4F9B;   /* Active/pressed state */
--color-secondary: #2C4A5A;         /* Secondary brand color */
```

### Gradients
```css
--gradient-primary: linear-gradient(90deg, #004AFF 0%, #5D87EE 50%, #FF5400 100%);
--gradient-primary-hover: linear-gradient(90deg, #0040DD 0%, #4D77DE 50%, #DD4400 100%);
```

### Semantic Colors
```css
--color-success: #52C41A;
--color-warning: #FAAD14;
--color-error: #F97066;
--color-info: #5D87EE;
```

### Light Mode
```css
--color-background: #E6E6E8;
--color-background-soft: #F5F5F5;
--color-background-mute: #FFFFFF;
--color-text: #222222;
--color-text-secondary: #666666;
--color-text-disabled: #999999;
--color-border: #E0E0E0;
```

### Dark Mode
```css
--color-background: #1A1A1A;
--color-background-soft: #2A2A2A;
--color-background-mute: #3A3A3A;
--color-text: #FFFFFF;
--color-text-secondary: #CCCCCC;
--color-text-disabled: #666666;
--color-border: #333333;
```

## Typography

### Font Family
```css
--font-primary: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

### Font Sizes
```css
--font-size-xs: 12px;
--font-size-sm: 14px;
--font-size-base: 16px;
--font-size-lg: 18px;
--font-size-xl: 20px;
--font-size-2xl: 24px;
```

### Font Weights
```css
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
```

## Spacing & Layout

### Spacing Scale
```css
--spacing-xs: 4px;
--spacing-sm: 8px;
--spacing-md: 16px;
--spacing-lg: 24px;
--spacing-xl: 32px;
--spacing-2xl: 48px;
```

### Border Radius
```css
--radius-sm: 4px;
--radius-md: 8px;
--radius-lg: 12px;
--radius-xl: 16px;
--radius-round: 9999px;
```

### Layout Principles
- Maximum content width: 1280px
- Card maximum width: 800px
- Modal maximum width: 720px
- Mobile breakpoint: 640px

## Components

### Cards
- Use Naive UI's `NCard` component
- Remove borders in favor of shadows
- Consistent padding: 16px (desktop), 12px (mobile)

### Headers
- Fixed position with z-index: 1000
- Height: 55px
- Background matches theme background color

### Footers
- Fixed bottom navigation
- Height: 70px
- Contains primary navigation buttons

## Forms

### Form Layout
```vue
<!-- Two-column layout for related fields -->
<div class="form-row">
  <n-form-item label="Field 1">
    <!-- input -->
  </n-form-item>
  <n-form-item label="Field 2">
    <!-- input -->
  </n-form-item>
</div>

<!-- Full-width for single fields -->
<n-form-item label="Field">
  <!-- input -->
</n-form-item>
```

### Form Sections
```vue
<div class="form-section">
  <h3 class="section-title">Section Name</h3>
  <!-- form fields -->
</div>
```

### Input Components
Always use Naive UI components:
- `NInput` for text inputs
- `NInputNumber` for numeric inputs
- `NSelect` for dropdowns
- `NDatePicker` for date/time selection
- `NCheckbox` for boolean options
- `NRadioGroup` for radio selections

### Property Type Options
```javascript
const propertyTypeOptions = [
  { label: 'Residential', value: 'residential' },
  { label: 'Commercial', value: 'commercial' }
]
```

### Package Type Options
```javascript
const packageTypeOptions = [
  { label: 'Basic Package', value: 'basic' },
  { label: 'Standard Package', value: 'standard' },
  { label: 'Premium Package', value: 'premium' }
]
```

## Buttons

### Gradient Button (Primary Action)
**ALL buttons in the app should use the gradient style**

```vue
<GradientButton @click="handleClick" :disabled="loading">
  Button Text
</GradientButton>
```

### Button Variants
```vue
<!-- Standard button -->
<GradientButton>Action</GradientButton>

<!-- Small button -->
<GradientButton :small-button="true">Action</GradientButton>

<!-- Full width button -->
<GradientButton :full-width="true">Action</GradientButton>
```

### Button States
- Default: Gradient background with shadow
- Hover: Slight lift with enhanced shadow
- Active: Return to baseline with reduced shadow
- Disabled: 60% opacity with no hover effects

## Modals

### Modal Structure
```vue
<Modal
  v-model:show="showModal"
  title="Modal Title"
  :mask-closable="false"
>
  <!-- Modal content -->
  <template #footer>
    <GradientButton @click="cancel">Cancel</GradientButton>
    <GradientButton @click="confirm">Confirm</GradientButton>
  </template>
</Modal>
```

### Modal Design Rules
- Maximum width: 720px (90vw on mobile)
- Padding: 12px 16px (reduced for compact feel)
- Use sections to organize content
- Two-column layout where appropriate
- Always use gradient buttons in footer

## Dark Mode

### Implementation
Dark mode is automatically detected using system preferences:
```javascript
const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
```

### Theme Configuration
```javascript
<n-config-provider :theme="theme" :theme-overrides="themeOverrides">
  <!-- App content -->
</n-config-provider>
```

### Design Considerations
- Ensure sufficient contrast in both modes
- Test all components in both light and dark themes
- Use semantic color variables that adapt to theme

## Code Conventions

### Component Organization
```
/components
  /ui           - Reusable UI components
  /modals       - Modal components
  /icons        - Icon components
```

### State Management
- Use Pinia stores for global state
- Prefix store names with `use` (e.g., `useAuthStore`, `useRequestsStore`)

### API Integration
- Base URL configured via environment variable: `VITE_API_URL`
- Always handle loading and error states
- Use proper TypeScript interfaces for data models

### Responsive Design
- Mobile-first approach
- Breakpoint at 640px
- Test on both mobile and desktop viewports

### Form Validation
Always validate before submission:
```javascript
const validateForm = () => {
  if (!formData.value.property_type) {
    message.error('Please select a property type')
    return false
  }
  // ... more validations
  return true
}
```

### Error Handling
- Display user-friendly error messages
- Log detailed errors to console for debugging
- Provide fallback UI for error states

### Performance
- Lazy load routes where appropriate
- Use computed properties for derived state
- Minimize re-renders with proper Vue reactivity

## Component Examples

### Request Card
```vue
<RequestItem 
  :item="requestData"
  :disabled="loading"
  @click="handleClick"
/>
```

### Loading State
```vue
<div class="loading-container">
  <n-spin size="medium" />
  <p>Loading...</p>
</div>
```

### Empty State
```vue
<div class="empty-state">
  <p class="empty-state-text">No items found</p>
  <GradientButton @click="createNew">
    Create New
  </GradientButton>
</div>
```

## Best Practices

1. **Consistency**: Always use the established patterns
2. **Accessibility**: Ensure proper labels and ARIA attributes
3. **Performance**: Optimize images and lazy load when possible
4. **Testing**: Test in both themes and on multiple devices
5. **Documentation**: Comment complex logic and update this guide when adding new patterns

## Version History

- v1.0.0 - Initial style guide creation
- Last updated: June 2024