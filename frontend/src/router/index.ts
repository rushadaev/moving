import { createRouter, createWebHistory } from 'vue-router'
import RequestsView from '../views/RequestsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: RequestsView,
    },
    {
      path: '/details',
      name: 'details',
      component: () => import('../views/DetailsView.vue'),
    },
    {
      path: '/tracking',
      name: 'tracking',
      component: () => import('../views/TrackingView.vue'),
    },
    {
      path: '/route',
      name: 'route',
      component: () => import('../views/RouteView.vue'),
    },
    {
      path: '/company',
      name: 'company',
      component: () => import('../views/CompanyView.vue'),
    },
  ],
})

export default router
