import { createRouter, createWebHistory } from 'vue-router'
import DashboardLayout from '../views/DashboardLayout.vue'
import { jwtDecode } from "jwt-decode";


import { useCache } from '@/utils/cache'

const cache = useCache()

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/preview',
      name: 'preview',
      component: DashboardLayout,
      children: [
        {
          path: '',
          name: 'preview-page',
          component: () => import('@/views/pages/preview/Preview.vue')
        },
        {
          path: 'dashboard',
          name: 'preview-dashboard',
          component: () => import('@/views/pages/preview/DashboardPreview.vue')
        },
        {
          path: 'process',
          name: 'preview-process',
          component: () => import('@/views/pages/preview/ProcessPreview.vue')
        },
        {
          path: 'status',
          name: 'preview-status',
          component: () => import('@/views/pages/preview/StatusPreview.vue')
        },
        {
          path: 'clients',
          name: 'preview-clients',
          component: () => import('@/views/pages/preview/ClientsPreview.vue')
        },
        {
          path: 'services',
          name: 'preview-services',
          component: () => import('@/views/pages/preview/ServicePreview.vue')
        },
        {
          path: 'roles',
          name: 'preview-roles',
          component: () => import('@/views/pages/preview/RolesPreview.vue')
        },
        {
          path: 'users',
          name: 'preview-users',
          component: () => import('@/views/pages/preview/UsersPreview.vue')
        },
        {
          path: 'search',
          name: 'preview-search',
          component: () => import('@/views/pages/preview/SearchPreview.vue')
        }
      ]
    },
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
  // Allow access to login, logout and preview routes without authentication
  if (to.name === 'login' || to.name === 'logout' || to.name === 'preview' || to.name === 'preview-page' || to.path.startsWith('/preview')) {
    return next();
  }

  const token = cache.getToken();

  if (!token) {
    return next({ name: 'login' });
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

    if (requiredPermission) {
      const getAuth = cache.getUser();
      
      if (!getAuth) {
        cache.clearStorage();
        return next({ name: 'login' });
      }

      try {
        const user = JSON.parse(getAuth);
        const roles = user?.roles || [];
        
        const hasPermission = roles.some((role: any) => {
          return role.permissions.some((permission: {name: string}) => permission.name === requiredPermission)
        });

        if(!hasPermission) {
          return next({ name: 'not-found' });
        }
      } catch (parseError) {
        // If user data is invalid, clear storage and redirect to login
        cache.clearStorage();
        return next({ name: 'login' });
      }
    }

    //enc check permission
  } catch (error) {
    // If token is invalid or any other error, clear storage and redirect to login
    cache.clearStorage();
    return next({ name: 'login' });
  }

  next();
});

export default router