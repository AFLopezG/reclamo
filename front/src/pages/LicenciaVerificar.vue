<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container class="public-page">
      <q-page class="row items-start justify-center">
        <q-card flat bordered class="public-card" style="width: 920px; max-width: 96vw">
          <q-card-section class="row items-center">
            <div>
              <div class="text-h6">Verificación de Licencia</div>
              <div class="text-caption text-grey-7">Código: {{ codigo }}</div>
            </div>
            <q-space />
            <q-badge v-if="loaded" :color="vigente ? 'green' : 'red'" :label="vigente ? 'VIGENTE' : 'NO VIGENTE'" />
          </q-card-section>

          <q-separator />

          <q-card-section v-if="loading">
            <q-linear-progress indeterminate color="primary" />
          </q-card-section>

          <q-card-section v-else-if="error">
            <div class="text-negative">{{ error }}</div>
          </q-card-section>

          <q-card-section v-else>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-subtitle2">Chofer</div>
                <div class="text-body1">{{ licencia?.chofer?.nombre }} {{ licencia?.chofer?.apellido }}</div>
                <div class="text-caption text-grey-7">CI: {{ licencia?.chofer?.cedula }} {{ licencia?.chofer?.comp }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2">Taxi</div>
                <div class="text-body1">PLACA: {{ licencia?.taxi?.placa || '-' }}</div>
                <div class="text-caption text-grey-7">{{ licencia?.taxi?.marca || '' }} {{ licencia?.taxi?.modelo || '' }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2">Licencia</div>
                <div class="text-body1">{{ licencia?.num_licencia }}</div>
                <div class="text-caption text-grey-7">Estado: {{ (licencia?.estado || 'VIGENTE').toString().toUpperCase() }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2">Vigencia</div>
                <div class="text-body1">Hasta: {{ licencia?.vigencia_hasta || '-' }}</div>
                <div class="text-caption text-grey-7">Hoy: {{ hoy }}</div>
              </div>
              <div class="col-12">
                <div class="text-subtitle2">Sindicato</div>
                <div class="text-body1">{{ licencia?.sindicato?.nombre || '-' }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'LicenciaVerificarPage',
  data() {
    return {
      loading: false,
      loaded: false,
      error: '',
      licencia: null,
      vigente: false,
      hoy: ''
    }
  },
  computed: {
    codigo() {
      return (this.$route?.params?.codigo || '').toString()
    }
  },
  watch: {
    codigo: {
      immediate: true,
      handler() {
        this.fetchData()
      }
    }
  },
  methods: {
    fetchData() {
      const codigo = this.codigo
      if (!codigo) return
      this.loading = true
      this.loaded = false
      this.error = ''
      this.$api
        .get(`licencias/verificar/${encodeURIComponent(codigo)}`)
        .then((res) => {
          this.licencia = res?.data?.licencia || null
          this.vigente = !!res?.data?.vigente
          this.hoy = res?.data?.hoy || ''
          this.loaded = true
        })
        .catch(() => {
          this.error = 'No se encontró la licencia o el código es inválido.'
        })
        .finally(() => {
          this.loading = false
        })
    }
  }
}
</script>
