import { route } from 'quasar/wrappers';
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router';
import routes from './routes';
import { LocalStorage } from 'quasar';

export default route(function () {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory);

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE)
  });

  Router.beforeEach((to, from, next) => {
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

    // Check if user is authenticated based on local storage tokens
    const isAuthenticatedApplicant = !!LocalStorage.getItem('access_token_applicant');
    const isAuthenticatedEmployer = !!LocalStorage.getItem('access_token_employer');

    if (requiresAuth && !isAuthenticatedApplicant && !isAuthenticatedEmployer) {
      next('/Login'); // Redirect to login if not authenticated
    } else {
      // Get the account type if available
      const user = LocalStorage.getItem('user');
      const parsedUser = user ? JSON.parse(user) : null;
      const accountType = parsedUser ? parseInt(parsedUser.account_type, 10) : null;

      // Apply route-specific checks
      if (to.path.startsWith('/applicant') && accountType !== 6) {
        next('/Login'); // Redirect to login if access denied
      } else if (to.path.startsWith('/employer') && accountType !== 5) {
        next('/Login'); // Redirect to login if access denied
      } else {
        next(); // Proceed to the route
      }
    }
  });

  return Router;
});
