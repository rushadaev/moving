import { createRouter, createWebHistory } from 'vue-router'
import RequestsView from '../views/RequestsView.vue'
import LandingView from '../views/LandingView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: LandingView,
    },
    {
      path: '/requests',
      name: 'requests',
      component: RequestsView,
      meta: { requiresAuth: true }
    },
    {
      path: '/auth',
      name: 'auth',
      component: () => import('../views/AuthView.vue'),
    },
    {
      path: '/details',
      name: 'details',
      component: () => import('../views/DetailsView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/tracking',
      name: 'tracking',
      component: () => import('../views/TrackingView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/route',
      name: 'route',
      component: () => import('../views/RouteView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/moving-history',
      name: 'moving-history',
      component: () => import('../views/MovingHistoryView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/company',
      name: 'company',
      component: () => import('../views/CompanyView.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

// Navigation guard to check authentication for protected routes
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Check if the route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Save the intended destination including query params
    const redirectPath = to.fullPath
    
    // Redirect to login page if not authenticated
    next({ 
      name: 'auth',
      // Store the redirect path to use after login
      query: { redirect: redirectPath }
    })
  } else {
    // Continue navigation
    next()
  }
})

export default router
