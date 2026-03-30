<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Sanciones</div>
          <div class="text-caption admin-subtitle">Gestión de tipos de sanción y montos</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-input dense outlined debounce="300" v-model="filter" class="admin-search" placeholder="Buscar (tipo, descripción)">
            <template v-slot:append><q-icon name="search" /></template>
          </q-input>
          <q-btn v-if="$store.hasPerm('sanciones.editar')" color="positive" icon="add_circle" label="Nueva sanción" @click="openCreate" />
        </div>
      </div>
    </div>

    <q-card flat bordered class="admin-card">
      <q-card-section class="row items-center q-pb-sm">
        <div class="text-subtitle1 text-weight-medium">Listado</div>
        <q-space />
        <q-badge color="grey-8" :label="`${rows.length} registros`" />
      </q-card-section>
      <q-separator />
      <q-card-section class="q-pa-none">
        <q-table dense flat :rows="rows" :columns="columns" row-key="id" :filter="filter" :rows-per-page-options="[25, 50, 100]">
          <template v-slot:body-cell-monto="props">
            <q-td key="monto" :props="props" class="text-right">
              {{ formatMonto(props.row.monto) }}
            </q-td>
          </template>
          <template v-slot:body-cell-op="props">
            <q-td key="op" :props="props">
              <q-btn v-if="$store.hasPerm('sanciones.editar')" dense round flat color="warning" icon="edit" @click="openEdit(props.row)" />
              <q-btn v-if="$store.hasPerm('sanciones.editar')" dense round flat color="red" icon="delete" @click="confirmDelete(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialogForm" persistent>
      <q-card style="min-width: 420px; max-width: 95vw;">
        <q-card-section class="bg-primary text-white">
          <div class="text-h7">
            <q-icon :name="form.id ? 'edit' : 'add_circle'" />
            {{ form.id ? 'MODIFICAR SANCIÓN' : 'REGISTRAR SANCIÓN' }}
          </div>
        </q-card-section>
        <q-card-section class="q-pt-sm">
          <q-form @submit="submitForm" class="q-gutter-sm">
            <q-input outlined dense v-model="form.tipo" label="Tipo" :rules="[val => !!val || 'Requerido']" />
            <q-input outlined dense v-model="form.descripcion" label="Descripción" />
            <q-input outlined dense v-model.number="form.monto" label="Monto (Bs)" type="number" step="0.01" min="0" :rules="[val => val !== null && val !== '' || 'Requerido']" />
            <div class="row justify-end q-gutter-sm">
              <q-btn flat color="grey-8" label="Cancelar" v-close-popup />
              <q-btn color="positive" icon="save" :label="form.id ? 'Guardar' : 'Registrar'" type="submit" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'SancionesPage',
  data() {
    return {
      filter: '',
      rows: [],
      dialogForm: false,
      form: { id: null, tipo: '', descripcion: '', monto: 0 },
      columns: [
        { label: 'TIPO', name: 'tipo', field: 'tipo', sortable: true },
        { label: 'DESCRIPCIÓN', name: 'descripcion', field: 'descripcion', sortable: true },
        { label: 'MONTO', name: 'monto', field: 'monto', align: 'right', sortable: true },
        { label: 'OP', name: 'op', field: 'op' }
      ]
    }
  },
  created() {
    if (!this.$store.hasPerm('sanciones.ver') && !this.$store.hasPerm('sanciones.editar')) {
      this.$router.replace('/navegador')
      return
    }
    this.loadAll()
  },
  methods: {
    formatMonto(v) {
      const n = Number(v || 0)
      return n.toFixed(2)
    },
    loadAll() {
      this.$api.get('sancion').then((res) => {
        this.rows = res.data || []
      })
    },
    openCreate() {
      this.form = { id: null, tipo: '', descripcion: '', monto: 0 }
      this.dialogForm = true
    },
    openEdit(row) {
      this.form = { id: row.id, tipo: row.tipo || '', descripcion: row.descripcion || '', monto: Number(row.monto || 0) }
      this.dialogForm = true
    },
    submitForm() {
      this.$q.loading.show()
      const payload = {
        tipo: (this.form.tipo || '').toString(),
        descripcion: (this.form.descripcion || '').toString(),
        monto: this.form.monto
      }
      const req = this.form.id
        ? this.$api.put('sancion/' + this.form.id, payload)
        : this.$api.post('sancion', payload)
      req.then(() => {
        this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Guardado' })
        this.dialogForm = false
        this.loadAll()
      }).catch((err) => {
        const msg = err?.response?.data?.message || 'Error al guardar'
        this.$q.notify({ message: msg, icon: 'error', color: 'red' })
      }).finally(() => this.$q.loading.hide())
    },
    confirmDelete(row) {
      this.$q.dialog({
        title: 'Eliminar',
        message: '¿Eliminar la sanción ' + (row.tipo || '') + '?',
        cancel: true,
        persistent: true
      }).onOk(() => this.deleteRow(row))
    },
    deleteRow(row) {
      this.$q.loading.show()
      this.$api.delete('sancion/' + row.id).then(() => {
        this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Eliminado' })
        this.loadAll()
      }).catch((err) => {
        const msg = err?.response?.data?.message || 'Error al eliminar'
        this.$q.notify({ message: msg, icon: 'error', color: 'red' })
      }).finally(() => this.$q.loading.hide())
    }
  }
}
</script>

