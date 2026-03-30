<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container class="public-page">
      <q-page class="row items-center justify-center">
        <q-card class="public-card q-pa-lg" style="width: 560px; max-width: 92vw">
          <div class="text-h2 text-weight-bold">404</div>
          <div class="text-subtitle1 text-grey-7 q-mt-sm">Página no encontrada</div>
          <q-btn class="q-mt-md" color="primary" unelevated to="/" label="Ir al inicio" no-caps />
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ErrorNotFound'
});
</script>
