import { RouteRecordRaw } from 'vue-router'
import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Reclamo from 'pages/Reclamo.vue'
import Reporte from 'pages/Reporte.vue'
import Usuario from 'pages/Usuario.vue'
import Login from 'pages/Login.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage, meta: { requiresAuth: true }},
      { path: '/usuario', component: Usuario, },
      { path: '/reporte', component: Reporte, }
    ],
  },
  {
    path:'/reclamo',
    component: Reclamo,

  },
  {
    path:'/login',
    component: Login,
    
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
