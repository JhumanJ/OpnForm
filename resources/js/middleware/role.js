import { useAuthStore } from '../stores/auth';

/**
 * This is middleware to check the current user role.
 *
 * middleware: 'role:admin,manager',
 */

export default (to, from, next, roles) => {
  const authStore = useAuthStore()
  
  // Split roles into an array
  roles = roles.split(',')

  // Check if the user has one of the required roles...
  if (!roles.includes(authStore.user?.role)) {
    next('/unauthorized')
  }

  next()
}
