import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Login from '../views/Login.vue'
import Roles from '../views/Roles.vue'
import Users from '../views/Users.vue'
import ExpenseCategories from '../views/ExpenseCategories.vue'
import Expenses from '../views/Expenses.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    props: true,
  },
  {
    path: '/roles',
    name: 'Roles',
    component: Roles,
  },

  {
    path: '/users',
    name: 'Users',
    component: Users,
  },

  {
    path: '/expense-categories',
    name: 'ExpenseCategories',
    component: ExpenseCategories,
  },

  {
    path: '/expenses',
    name: 'Expenses',
    component: Expenses,
  },

]

const router = createRouter({
  history: createWebHistory('/'),
  routes
})

export default router
