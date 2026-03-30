<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Reporte de multas</div>
          <div class="text-caption admin-subtitle">Consulta por fechas, placa o CI conductor</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-input dense outlined v-model="report.ini" type="date" label="Fecha ini" />
          <q-input dense outlined v-model="report.fin" type="date" label="Fecha fin" />
          <q-btn color="primary" icon="search" label="Buscar" @click="buscar" />
          <q-btn color="info" icon="picture_as_pdf" label="PDF" @click="downloadPdf" />
        </div>
      </div>
      <div class="row q-col-gutter-md q-mt-sm">
        <div class="col-12 col-md-3">
          <q-input dense outlined v-model="report.placa" label="Placa (opcional)" />
        </div>
        <div class="col-12 col-md-3">
          <q-input dense outlined v-model="report.cedula_conductor" label="CI Conductor (opcional)" />
        </div>
      </div>
    </div>

    <q-card flat bordered class="admin-card">
      <q-card-section class="row items-center q-pb-sm">
        <div class="text-subtitle1 text-weight-medium">Listado</div>
        <q-space />
        <q-input outlined dense debounce="300" v-model="filter" class="admin-search" placeholder="Buscar en tabla">
          <template v-slot:append><q-icon name="search" /></template>
        </q-input>
      </q-card-section>
      <q-separator />
      <q-card-section class="q-pa-none">
        <q-table dense flat :rows="rows" :columns="columns" row-key="id" :filter="filter" :rows-per-page-options="[25, 50, 100]">
          <template v-slot:body-cell-monto="props">
            <q-td key="monto" :props="props" class="text-right">
              {{ Number(props.row?.sancion?.monto || 0).toFixed(2) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { date } from 'quasar'

export default {
  name: 'MultasReportePage',
  data() {
    return {
      filter: '',
      rows: [],
      report: {
        ini: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fin: date.formatDate(new Date(), 'YYYY-MM-DD'),
        placa: '',
        cedula_conductor: ''
      },
      columns: [
        { label: 'FECHA/HORA', name: 'fecha_hora', field: (r) => r?.fecha_hora, sortable: true },
        { label: 'PLACA', name: 'placa', field: 'placa', sortable: true },
        { label: 'LICENCIA', name: 'num_licencia', field: 'num_licencia', sortable: true },
        {
          label: 'PROPIETARIO',
          name: 'propietario',
          field: (r) => ((r?.taxi?.propietario?.nombre || '') + ' ' + (r?.taxi?.propietario?.apellido || '')).trim(),
          sortable: true
        },
        {
          label: 'CONDUCTOR',
          name: 'conductor',
          field: (r) => ((r?.licencia?.chofer?.nombre || '') + ' ' + (r?.licencia?.chofer?.apellido || '')).trim(),
          sortable: true
        },
        { label: 'SANCIÓN', name: 'sancion', field: (r) => r?.sancion?.tipo || '', sortable: true },
        { label: 'MONTO', name: 'monto', field: 'monto', align: 'right' },
        { label: 'USUARIO', name: 'usuario', field: (r) => r?.user?.nombre || r?.user?.name || '', sortable: true }
      ]
    }
  },
  created() {
    if (!this.$store.hasPerm('multas.reporte')) {
      this.$router.replace('/navegador')
      return
    }
    this.buscar()
  },
  methods: {
    buscar() {
      this.$q.loading.show()
      this.$api.post('multa/reporte', this.report).then((res) => {
        this.rows = res.data || []
      }).catch((err) => {
        const msg = err?.response?.data?.message || 'Error al buscar'
        this.$q.notify({ message: msg, icon: 'error', color: 'red' })
      }).finally(() => this.$q.loading.hide())
    },
    downloadPdf() {
      this.$q.loading.show()
      this.$api.get('multa/reporte/pdf', { params: this.report, responseType: 'blob' }).then((res) => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'multas_' + this.report.ini + '_al_' + this.report.fin + '.pdf')
        document.body.appendChild(link)
        link.click()
      }).catch(() => {
        this.$q.notify({ message: 'Error al generar PDF', icon: 'error', color: 'red' })
      }).finally(() => this.$q.loading.hide())
    }
  }
}
</script>
