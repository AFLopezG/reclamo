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

      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
    <q-list bordered class="rounded-borders">
            <q-item-label header class="text-center text-bold bg-blue-8 text-white">
            Opciones
          </q-item-label>
          <q-item clickable dense to="/navegador" exact active-class="bg-primary text-white">
            <q-item-section avatar><q-icon name="home" /></q-item-section>
            <q-item-section><q-item-label>Principal</q-item-label><q-item-label caption class="text-grey-2"></q-item-label></q-item-section>
        </q-item>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="people" label="Usuarios" to="/usuario" expand-icon="null" v-if="store.user.id==1"/>
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="summarize" label="Reporte" to="/reporte" expand-icon="null" />
        <q-expansion-item  active-class="bg-primary text-white" dense exact expand-separator icon="airport_shuttle" label="Inspecion" to="/inspeccion" expand-icon="null" />
      

        
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
    }
  }
});
</script>
