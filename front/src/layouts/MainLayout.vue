<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title class="text-weight-bold">
          AUTORIDAD DE TRANSPORTE MUNICIPAL ORURO
        </q-toolbar-title>
        <q-space />
        <div class="row items-center q-gutter-sm">
          <div class="text-caption text-white">
            {{ store.user?.nombre || store.user?.name || '' }}
          </div>
          <q-btn flat dense icon="logout" label="Salir" @click="logout" />
        </div>
      </q-toolbar>
      
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
    <q-list bordered class="rounded-borders">
            <q-item-label header class="text-center text-bold bg-red-10 text-white">
            Opciones
          </q-item-label>
          <q-item clickable dense to="/navegador" exact active-class="bg-primary text-white">
            <q-item-section avatar><q-icon name="home" /></q-item-section>
            <q-item-section><q-item-label>Principal</q-item-label><q-item-label caption class="text-grey-2"></q-item-label></q-item-section>
        </q-item>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="people" label="Usuarios" to="/usuario" expand-icon="null" v-if="store.hasPerm('usuarios.ver')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="admin_panel_settings" label="Roles y Permisos" to="/roles" expand-icon="null" v-if="store.hasPerm('roles.editar')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="summarize" label="Reporte" to="/reporte" expand-icon="null" v-if="store.hasPerm('reporte.ver')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="airport_shuttle" label="Inspecion" to="/inspeccion" expand-icon="null" v-if="store.hasPerm('inspeccion.ver')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="badge" label="Licencias" to="/licencias" expand-icon="null" v-if="store.hasPerm('licencias.ver')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="gavel" label="Sanciones" to="/sanciones" expand-icon="null" v-if="store.hasPerm('sanciones.ver') || store.hasPerm('sanciones.editar')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="receipt_long" label="Multas" to="/multas" expand-icon="null" v-if="store.hasPerm('multas.registrar')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="summarize" label="Reporte Multas" to="/multas-reporte" expand-icon="null" v-if="store.hasPerm('multas.reporte')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="groups" label="Sindicatos" to="/sindicatos" expand-icon="null" v-if="store.hasPerm('sindicatos.editar')"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="map" label="Posicion Personal" to="/posicion" expand-icon="null" v-if="store.hasPerm('posicion.ver')"/>
      

        
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script >

import { defineComponent, ref } from 'vue'
import {globalStore} from 'stores/globalStore'
export default defineComponent({
  name: 'MainLayout',
  data () {
    return {
      leftDrawerOpen: ref(false),
      store: globalStore(),
      valid:false
    }
  },
  created () {
    if(!this.store.isLoggedIn)
      this.$router.push('/login')

  },
  methods:{
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    logout () {
      this.$q.loading.show()
      const id = this.store?.user?.id
      this.$api.post('logout', { id }).catch(() => {
        this.$q.dialog({
          title: 'Error',
          message: 'No se pudo cerrar sesión en el servidor. Se cerrará la sesión localmente.',
          ok: 'Aceptar'
        })
      }).finally(() => {
        this.$api.defaults.headers.common.Authorization = ''
        localStorage.removeItem('tokenReclamo')
        this.store.clearAuth()
        this.store.isLoggedIn = false
        this.$q.loading.hide()
        this.$router.push('/login')
      })
    }
  }
});
</script>
