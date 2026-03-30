<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Sindicatos</div>
          <div class="text-caption admin-subtitle">Gestionar sindicatos</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-input dense outlined debounce="300" v-model="filter" class="admin-search" placeholder="Buscar por nombre">
            <template v-slot:append><q-icon name="search" /></template>
          </q-input>
          <q-btn color="positive" icon="add_circle" label="Nuevo" @click="openNew" />
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
          <template v-slot:body-cell-op="props">
            <q-td key="op" :props="props">
              <q-btn dense round flat color="warning" icon="edit" @click="openEdit(props.row)" />
              <q-btn dense round flat color="red" icon="delete" @click="confirmDelete(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialogForm" persistent>
      <q-card style="min-width: 420px; max-width: 95vw">
        <q-card-section class="bg-primary text-white">
          <div class="text-h7">
            <q-icon :name="form.id ? 'edit' : 'add_circle'" />
            {{ form.id ? 'Editar sindicato' : 'Nuevo sindicato' }}
          </div>
        </q-card-section>
        <q-card-section class="q-pt-sm">
          <q-form @submit="submitForm" class="q-gutter-sm">
            <q-input outlined dense v-model="form.nombre" label="Nombre" :rules="[(v) => !!v || 'Requerido']" />
            <div class="row q-mt-md">
              <q-btn color="positive" icon="save" :label="form.id ? 'Guardar' : 'Crear'" type="submit" />
              <q-btn color="negative" icon="close" label="Cancelar" class="q-ml-sm" v-close-popup />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'SindicatosPage',
  data() {
    return {
      rows: [],
      filter: '',
      dialogForm: false,
      form: { id: null, nombre: '' },
      columns: [
        { name: 'nombre', label: 'NOMBRE', field: 'nombre', align: 'left', sortable: true },
        { name: 'op', label: 'OP', field: 'op', align: 'center' }
      ]
    }
  },
  created() {
    if (!this.$store.hasPerm('sindicatos.editar')) {
      this.$router.replace('/navegador')
      return
    }
    this.loadAll()
  },
  methods: {
    loadAll() {
      this.$q.loading.show()
      this.$api
        .get('sindicato')
        .then((res) => {
          this.rows = res.data || []
        })
        .catch(() => {
          this.$q.notify({ message: 'No se pudo cargar sindicatos', icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    openNew() {
      this.form = { id: null, nombre: '' }
      this.dialogForm = true
    },
    openEdit(row) {
      this.form = { id: row.id, nombre: row.nombre || '' }
      this.dialogForm = true
    },
    submitForm() {
      const payload = { nombre: this.form.nombre }
      const req = this.form.id ? this.$api.put(`sindicato/${this.form.id}`, payload) : this.$api.post('sindicato', payload)
      this.$q.loading.show()
      req
        .then(() => {
          this.$q.notify({ message: 'Guardado', icon: 'check', color: 'green' })
          this.dialogForm = false
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'Error al guardar'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    confirmDelete(row) {
      this.$q
        .dialog({
          title: 'Eliminar',
          message: `¿Eliminar el sindicato ${row.nombre}?`,
          cancel: true,
          persistent: true
        })
        .onOk(() => this.deleteRow(row))
    },
    deleteRow(row) {
      this.$q.loading.show()
      this.$api
        .delete(`sindicato/${row.id}`)
        .then(() => {
          this.$q.notify({ message: 'Eliminado', icon: 'delete', color: 'green' })
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'No se pudo eliminar'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    }
  }
}
</script>

