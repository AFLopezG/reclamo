import { RouteRecordRaw } from 'vue-router'
import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Reclamo from 'pages/Reclamo.vue'
import Reporte from 'pages/Reporte.vue'
import Usuario from 'pages/Usuario.vue'
import Login from 'pages/Login.vue'
import Inspeccion from 'pages/Inspeccion.vue'
import Posicion from 'pages/Posicion.vue'
import Licencias from 'pages/Licencias.vue'
import RolesPermisos from 'pages/RolesPermisos.vue'
import LicenciaVerificar from 'pages/LicenciaVerificar.vue'
import Sindicatos from 'pages/Sindicatos.vue'
import Sanciones from 'pages/Sanciones.vue'
import Multas from 'pages/Multas.vue'
import MultasReporte from 'pages/MultasReporte.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/navegador',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', component: IndexPage },
      { path: '/usuario', component: Usuario, },
      { path: '/roles', component: RolesPermisos, },
      { path: '/reporte', component: Reporte, },
      { path: '/inspeccion', component: Inspeccion, },
      { path: '/posicion', component: Posicion, },
      { path: '/licencias', component: Licencias, },
      { path: '/sindicatos', component: Sindicatos, },
      { path: '/sanciones', component: Sanciones, },
      { path: '/multas', component: Multas, },
      { path: '/multas-reporte', component: MultasReporte, },
    ],
  },
  {
    path: '/licencias/verificar/:codigo',
    component: LicenciaVerificar,
  },
  {
    path:'/',
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
