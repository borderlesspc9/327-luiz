import { createRouter, createWebHistory } from 'vue-router'
import DashboardLayout from '../views/DashboardLayout.vue'
import { jwtDecode } from "jwt-decode";


import { useCache } from '@/utils/cache'

const cache = useCache()

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/pages/auth/Login.vue')
    },
    {
      path: '/logout',
      name: 'logout',
      component: () => import('@/views/pages/auth/Logout.vue')
    },
    {
      path: '/',
      name: 'home',
      component: DashboardLayout
    },
    {
      path: '/painel',
      name: 'painel',
      component: () => import('@/views/DashboardLayout.vue'),
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('@/views/pages/Dashboard.vue')
        },
        {
          path: 'clients',
          name: 'clients',
          component: () => import('@/views/pages/clients/Clients.vue'),
          meta: { permission: 'Read client' }
        },
        {
          path: 'services',
          name: 'services',
          component: () => import('@/views/pages/services/Service.vue'),
          meta: { permission: 'Read service'}
        },
        {
          path: 'process',
          name: 'process',
          component: () => import('@/views/pages/process/Process.vue'),
          meta: { permission: 'Read process'}
        },
        {
          path: 'process/status',
          name: 'status',
          component: () => import('@/views/pages/process/Status.vue'),
          meta: { permission: 'Read status'}
        },
        {
          path:'roles',
          name: 'roles',
          component: () => import('@/views/pages/roles/Roles.vue'),
          meta: { permission: 'Read roles'}
        },
        {
          path: 'users',
          name: 'users',
          component: () => import('@/views/pages/users/Users.vue'),
          meta: { permission: 'Read user'}
        },
        {
          path: 'search',
          name: 'search',
          component: () => import('@/views/pages/search/Search.vue'),
        },
        {
          path: '/:catchAll(.*)',
          name: 'not-found',
          component: () => import('@/views/pages/not-found/NotFound.vue')
        }
      ]
    },
  ]
})

router.beforeEach((to, from, next) => {
  const token = cache.getToken();

  if (!token) {
    if (to.name !== 'login') {
      return next({ name: 'login' });
    } else {
      return next();
    }
  }

  try {

  // Check if token is valid
  const decodedToken = jwtDecode(token);
  const currentTime = Date.now() / 1000;

  if (!decodedToken.exp || decodedToken.exp < currentTime) {
    // Token has expired
    cache.clearStorage();
    return next({ name: 'login' });
  }
  //end check token

  // Check if user has permission to access the route
  const requiredPermission = to.meta?.permission;

  const getAuth = cache.getUser();
  const user = JSON.parse(getAuth);
  const roles = user?.roles || [];
  let hasPermission = null;

  if(requiredPermission) {
    hasPermission = roles.some((role: any) => {
      return role.permissions.some((permission: {name: string}) => permission.name === requiredPermission)
    });

    if(!hasPermission) {
      return next('/404');
    }
  }

  //enc check permission
  } catch (error) {

    return next({ name: 'login' });
  }

  next();
});

export default router