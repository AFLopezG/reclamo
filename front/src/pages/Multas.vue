<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Multas</div>
          <div class="text-caption admin-subtitle">Buscar por placa y registrar multa</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-input dense outlined v-model="placa" label="Placa" @keyup.enter="buscar" />
          <q-btn color="primary" icon="search" label="Buscar" @click="buscar" />
        </div>
      </div>
    </div>

    <q-card v-if="mensaje" flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <q-icon :name="exists ? 'check_circle' : 'error'" :color="exists ? 'green' : 'red'" size="md" class="q-mr-sm" />
        <div>{{ mensaje }}</div>
        <q-space />
        <q-badge v-if="exists && licencia" :color="vigente ? 'green' : 'red'" :label="vigente ? 'VIGENTE' : 'VENCIDO'" />
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-7">
        <q-card v-if="taxi" flat bordered class="admin-card">
          <q-card-section>
            <div class="text-subtitle1 text-weight-medium">Datos principales</div>
            <q-separator class="q-my-sm" />
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4"><b>Placa:</b> {{ taxi.placa }}</div>
              <div class="col-12 col-md-4"><b>Marca:</b> {{ taxi.marca || '-' }}</div>
              <div class="col-12 col-md-4"><b>Modelo:</b> {{ taxi.modelo || '-' }}</div>
              <div class="col-12 col-md-6"><b>Propietario:</b> {{ fullName(taxi.propietario) }}</div>
              <div class="col-12 col-md-6"><b>CI Prop.:</b> {{ taxi.propietario?.cedula || '-' }} {{ taxi.propietario?.comp || '' }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-5">
        <q-card flat bordered class="admin-card">
          <q-card-section>
            <div class="text-subtitle1 text-weight-medium">Registrar multa</div>
            <q-separator class="q-my-sm" />
            <q-form @submit="registrar" class="q-gutter-sm">
              <q-select
                outlined
                dense
                v-model="sancionSel"
                :options="sanciones"
                option-label="label"
                option-value="id"
                emit-value
                map-options
                label="Tipo de sanción"
                :disable="!taxi || !licencia"
                :rules="[val => !!val || 'Requerido']"
              />
              <q-input outlined dense :value="sancionDetalle" label="Detalle" type="textarea" autogrow disable />
              <q-input outlined dense :value="sancionMonto" label="Monto (Bs)" disable />
              <q-btn color="positive" icon="save" label="Registrar multa" type="submit" :disable="!taxi || !licencia || !sancionSel" />
            </q-form>
          </q-card-section>
          <q-separator />
          <q-card-section v-if="licencia">
            <div class="text-subtitle2 text-weight-medium">Licencia</div>
            <div class="q-mt-xs"><b>N°:</b> {{ licencia.num_licencia }}</div>
            <div><b>Chofer:</b> {{ fullName(licencia.chofer) }}</div>
            <div><b>CI Chofer:</b> {{ licencia.chofer?.cedula || '-' }} {{ licencia.chofer?.comp || '' }}</div>
            <div class="q-mt-xs"><b>Vigencia hasta:</b> {{ licencia.vigencia_hasta || '-' }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'MultasPage',
  data() {
    return {
      placa: '',
      mensaje: '',
      exists: false,
      vigente: false,
      taxi: null,
      licencia: null,
      sanciones: [],
      sancionSel: null
    }
  },
  computed: {
    sancionObj() {
      return this.sanciones.find((s) => s.id === this.sancionSel) || null
    },
    sancionDetalle() {
      return this.sancionObj?.descripcion || ''
    },
    sancionMonto() {
      const n = Number(this.sancionObj?.monto || 0)
      return n.toFixed(2)
    }
  },
  created() {
    if (!this.$store.hasPerm('multas.registrar')) {
      this.$router.replace('/navegador')
      return
    }
    this.loadSanciones()
  },
  methods: {
    fullName(p) {
      if (!p) return '-'
      return ((p.nombre || '') + ' ' + (p.apellido || '')).trim() || '-'
    },
    loadSanciones() {
      this.$api.get('sancion').then((res) => {
        const list = res.data || []
        this.sanciones = list.map((s) => ({
          ...s,
          label: (s.tipo || '') + ' - Bs ' + Number(s.monto || 0).toFixed(2)
        }))
      })
    },
    buscar() {
      const placa = (this.placa || '').toString().trim()
      if (!placa) return
      this.$q.loading.show()
      this.mensaje = ''
      this.exists = false
      this.vigente = false
      this.taxi = null
      this.licencia = null
      this.sancionSel = null
      this.$api.get('multa/buscar', { params: { placa } }).then((res) => {
        this.mensaje = res.data?.message || ''
        this.exists = !!res.data?.exists
        this.vigente = !!res.data?.vigente
        this.taxi = res.data?.taxi || null
        this.licencia = res.data?.licencia || null
      }).catch((err) => {
        this.mensaje = err?.response?.data?.message || 'Error al buscar'
        this.exists = false
      }).finally(() => this.$q.loading.hide())
    },
    registrar() {
      if (!this.taxi || !this.licencia || !this.sancionSel) return
      this.$q.loading.show()
      this.$api.post('multa', { placa: this.taxi.placa, sancion_id: this.sancionSel }).then(() => {
        this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Multa registrada' })
        this.sancionSel = null
      }).catch((err) => {
        const msg = err?.response?.data?.message || 'Error al registrar multa'
        this.$q.notify({ message: msg, icon: 'error', color: 'red' })
      }).finally(() => this.$q.loading.hide())
    }
  }
}
</script>

