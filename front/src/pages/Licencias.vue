<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Licencias</div>
          <div class="text-caption admin-subtitle">Registro, renovacion y verificacion rapida</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-input
            dense
            outlined
            debounce="300"
            v-model="filter"
            class="admin-search"
            placeholder="Buscar (licencia, chofer, propietario, placa)"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-btn-dropdown v-if="$store.hasPerm('licencias.imprimir')" color="info" icon="picture_as_pdf" label="Imprimir lista">
            <q-list>
              <q-item clickable v-close-popup @click="downloadListaPdf('todos')">
                <q-item-section>Todos</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="downloadListaPdf('vigentes')">
                <q-item-section>Vigentes</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="downloadListaPdf('vencidas')">
                <q-item-section>Vencidas</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
          <q-btn v-if="$store.hasPerm('contribuyentes.editar')" color="grey-10" icon="manage_accounts" label="Editar Persona" @click="openContribuyenteEdit" />
          <q-btn v-if="$store.hasPerm('licencias.editar')" color="positive" icon="add_circle" label="Registrar licencia" @click="openCreate" />
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
        <q-table
          dense
          flat
          :rows="rows"
          :columns="columns"
          row-key="id"
          :filter="filter"
          :rows-per-page-options="[25, 50, 100]"
        >
          <template v-slot:body-cell-chofer_foto="props">
            <q-td key="chofer_foto" :props="props">
              <q-avatar size="34px" rounded class="bg-grey-3">
                <img v-if="props.row.chofer?.foto_url" :src="props.row.chofer.foto_url" alt="Foto chofer" />
                <q-icon v-else name="person" color="grey-8" />
              </q-avatar>
            </q-td>
          </template>

          <template v-slot:body-cell-estado="props">
            <q-td key="estado" :props="props">
              <q-badge :color="estadoColor(props.row)" :label="estadoLabel(props.row)" />
            </q-td>
          </template>

          <template v-slot:body-cell-op="props">
            <q-td key="op" :props="props">
              <q-btn v-if="$store.hasPerm('licencias.editar')" dense round flat color="warning" icon="edit" @click="openEdit(props.row)" />
              <q-btn v-if="$store.hasPerm('licencias.renovar')" dense round flat color="teal" icon="autorenew" :disable="!isVencida(props.row)" @click="confirmRenovar(props.row)" />
              <q-btn v-if="$store.hasPerm('licencias.anular')" dense round flat color="red" icon="block" @click="confirmAnular(props.row)" />
              <q-btn v-if="$store.hasPerm('licencias.imprimir')" dense round flat color="primary" icon="badge" :disable="!isVigente(props.row)" @click="downloadCredencial(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialogForm" full-width>
      <q-card>
        <q-card-section class="bg-primary text-white">
          <div class="text-h7">
            <q-icon :name="form.id ? 'edit' : 'add_circle'" />
            {{ form.id ? 'MODIFICAR LICENCIA' : 'REGISTRAR LICENCIA' }}
          </div>
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <q-form @submit="submitForm" class="q-gutter-sm">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="form.num_licencia" label="N° Licencia" disable placeholder="Se genera automaticamente" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="form.fecha_licencia" type="date" label="Fecha inicio" disable />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="form.vigencia_hasta" type="date" label="Vigencia hasta" disable />
              </div>

              <div class="col-12 col-md-4">
                <q-select outlined dense v-model="form.tipo" :options="tipos" label="Tipo" @update:model-value="onTipoChange" />
              </div>
              <div class="col-12 col-md-4">
                <q-select outlined dense v-model="form.estado" :options="estados" label="Estado" />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  outlined
                  dense
                  v-model="form.sindicato_id"
                  :options="sindicatos"
                  option-label="nombre"
                  option-value="id"
                  emit-value
                  map-options
                  label="Sindicato"
                  clearable
                  :disable="(form.tipo || '').toUpperCase() !== 'SINDICATO'"
                  :rules="[(v) => ((form.tipo || '').toUpperCase() !== 'SINDICATO' ? true : !!v || 'Requerido')]"
                />
              </div>
            </div>

            <q-separator spaced />

            <div class="text-subtitle2 q-mb-xs"><q-icon name="assignment_ind" /> Datos del propietario</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.propietario.cedula" label="CI" :rules="[(v) => !!v || 'Requerido']" @blur="lookupContribuyente('propietario')" />
              </div>
              <div class="col-12 col-md-2">
                <q-input outlined dense v-model="form.propietario.comp" label="Complemento" @blur="lookupContribuyente('propietario')" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="form.propietario.nombre" label="Nombre" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.propietario.apellido" label="Apellido" :rules="[(v)=> !!v || 'Requerido']"/>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.propietario.telefono" label="Teléfono" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined dense v-model="form.propietario.direccion" label="Direccion" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-select outlined dense v-model="form.propietario.categoria" :options="categorias" label="Categoría" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.propietario.fecha_nacimiento" type="date" label="Fecha nacimiento" />
              </div>
            </div>

            <q-separator spaced />

            <div class="text-subtitle2 q-mb-xs"><q-icon name="person" /> Datos del chofer</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.chofer.cedula" label="CI" :rules="[(v) => !!v || 'Requerido']" @blur="lookupContribuyente('chofer')" />
              </div>
              <div class="col-12 col-md-2">
                <q-input outlined dense v-model="form.chofer.comp" label="Complemento" @blur="lookupContribuyente('chofer')" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="form.chofer.nombre" label="Nombre" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.chofer.apellido" label="Apellido" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.chofer.telefono" label="Teléfono" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined dense v-model="form.chofer.direccion" label="Dirección" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-select outlined dense v-model="form.chofer.categoria" :options="categorias" label="Categoría" :rules="[(v) => !!v || 'Requerido']"/>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.chofer.fecha_nacimiento" type="date" label="Fecha nacimiento" />
              </div>
            </div>

            <q-separator spaced />

            <div class="text-subtitle2 q-mb-xs"><q-icon name="airport_shuttle" /> Datos del taxi</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.placa" label="Placa" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.marca" label="Marca" :rules="[(v) => !!v || 'Requerido']"/>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.modelo" label="Modelo" :rules="[(v) => !!v || 'Requerido']" />
              </div>
                <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.color" label="Color" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.anio" label="Año" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.ruat" label="RUAT" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.soat" label="SOAT" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.taxi.fecha_soat" type="date" label="Fecha SOAT" :rules="[(v) => !!v || 'Requerido']" />
              </div>
            </div>
            <div class="q-mt-md">
              <q-btn color="positive" :label="form.id ? 'Modificar' : 'Registrar'" type="submit" icon="save" />
              <q-btn color="negative" label="Cancelar" icon="close" v-close-popup class="q-ml-sm" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogContribuyente" full-width>
      <q-card class="admin-modal">
        <q-card-section class="admin-modal-header">
          <div class="row items-center">
            <div class="text-subtitle1 text-weight-medium" v-if="contribuyenteEdit.id">Editar Contribuyente</div>
            <div class="text-subtitle1 text-weight-medium" v-else>Registrar Contribuyente</div>
            <q-space />
            <q-btn dense round flat icon="close" v-close-popup />
          </div>
          <div class="text-caption">Busca por CI y complemento, luego modifica o regsitrar y guarda</div>
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pt-sm">
          <q-form @submit="saveContribuyente" class="q-gutter-sm">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="contribuyenteEdit.cedula" label="CI" :rules="[(v) => !!v || 'Requerido']" @blur="lookupContribuyenteEdit" />
              </div>
              <div class="col-12 col-md-2">
                <q-input outlined dense v-model="contribuyenteEdit.comp" label="Complemento" @blur="lookupContribuyenteEdit" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined dense v-model="contribuyenteEdit.nombre" label="Nombre" :rules="[(v) => !!v || 'Requerido']" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="contribuyenteEdit.apellido" label="Apellido" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="contribuyenteEdit.telefono" label="Telefono" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined dense v-model="contribuyenteEdit.direccion" label="Direccion" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="contribuyenteEdit.categoria" label="Categoria" />
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="contribuyenteEdit.fecha_nacimiento" type="date" label="Fecha nacimiento" />
              </div>
            </div>

            <q-separator spaced />

            <div class="text-subtitle2 q-mb-xs"><q-icon name="photo_camera" /> Fotografía (opcional)</div>
            <div class="row q-col-gutter-sm items-center">
              <div class="col-12 col-md-3">
                <q-avatar size="86px" rounded class="bg-grey-3">
                  <img v-if="contribuyenteEditFotoPreview" :src="contribuyenteEditFotoPreview" alt="Foto" />
                  <q-icon v-else name="person" color="grey-8" size="48px" />
                </q-avatar>
              </div>
              <div class="col-12 col-md-9">
                <q-file
                  outlined
                  dense
                  clearable
                  v-model="contribuyenteEditFotoFile"
                  label="Subir foto"
                  accept="image/*"
                  @update:model-value="onContribuyenteFotoChange"
                />
                <div class="text-caption text-grey-7">Si no sube foto, se mantiene la actual.</div>
              </div>
            </div>

            <div class="row q-mt-md">
              <q-btn color="amber-10" label="Actualizar contribuyente" type="submit" icon="save" v-if="contribuyenteEdit.id"/>
              <q-btn color="green-10" label="Registrar contribuyente" type="submit" icon="save" v-else />
              <q-btn color="negative" label="Cancelar" icon="close" v-close-popup class="q-ml-sm" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { date } from 'quasar'

export default {
  name: 'LicenciasPage',
  data() {
    return {
      filter: '',
      rows: [],
      sindicatos: [],
      dialogForm: false,
      dialogContribuyente: false,
      contribuyenteEdit: this.emptyContribuyente(),
      contribuyenteEditFotoFile: null,
      contribuyenteEditFotoPreview: '',
      estados: ['ACTIVO', 'VENCIDO', 'ANULADO'],
      tipos: ['SINDICATO', 'INDEPENDIENTE'],
      categorias: ['A', 'B', 'C', 'D', 'E'],
      form: this.emptyForm(),
      columns: [
        { name: 'op', label: 'OP', field: 'op' },
        { name: 'num_licencia', label: 'N° LICENCIA', field: 'num_licencia', sortable: true },
        { name: 'placa', label: 'PLACA', field: (r) => r.taxi?.placa, sortable: true },
        { name: 'chofer_foto', label: 'FOTO', field: (r) => r.chofer?.foto_url || '' },
        { name: 'chofer', label: 'CHOFER', field: (r) => `${r.chofer?.nombre || ''} ${r.chofer?.apellido || ''}`.trim(), sortable: true },
        { name: 'propietario', label: 'PROPIETARIO', field: (r) => `${r.taxi?.propietario?.nombre || ''} ${r.taxi?.propietario?.apellido || ''}`.trim(), sortable: true },
        { name: 'sindicato', label: 'SINDICATO', field: (r) => r.sindicato?.nombre || '-', sortable: true },
        { name: 'tipo', label: 'TIPO', field: 'tipo', sortable: true },
        { name: 'estado', label: 'ESTADO', field: 'estado', sortable: true },
        { name: 'vigencia_hasta', label: 'VIGENCIA HASTA', field: 'vigencia_hasta', sortable: true }
      ]
    }
  },
  created() {
    if (!this.$store.hasPerm('licencias.ver')) {
      this.$router.replace('/navegador')
      return
    }
    this.loadAll()
  },
  methods: {
    emptyContribuyente() {
      return {
        cedula: '',
        comp: '',
        nombre: '',
        apellido: '',
        fecha_nacimiento: null,
        telefono: '',
        direccion: '',
        categoria: '',
        foto_url: null
      }
    },
    emptyForm() {
      const hoy = date.formatDate(new Date(), 'YYYY-MM-DD')
      const hasta = date.formatDate(date.addToDate(new Date(), { year: 1 }), 'YYYY-MM-DD')
      return {
        id: null,
        num_licencia: '',
        fecha_licencia: hoy,
        vigencia_hasta: hasta,
        tipo: 'INDEPENDIENTE',
        estado: 'ACTIVO',
        sindicato_id: null,
        taxi: {
          placa: '',
          marca: '',
          modelo: '',
          linea: '',
          color: '',
          anio: '',
          chasis: '',
          ruat: ''
        },
        propietario: {
          cedula: '',
          comp: '',
          nombre: '',
          apellido: '',
          fecha_nacimiento: null,
          telefono: '',
          direccion: '',
          categoria: ''
        },
        chofer: {
          cedula: '',
          comp: '',
          nombre: '',
          apellido: '',
          fecha_nacimiento: null,
          telefono: '',
          direccion: '',
          categoria: ''
        }
      }
    },
    loadAll() {
      this.$q.loading.show()
      Promise.all([this.$api.get('licencia'), this.$api.get('sindicato')])
        .then(([licRes, sinRes]) => {
          this.rows = licRes.data || []
          this.sindicatos = sinRes.data || []
        })
        .finally(() => this.$q.loading.hide())
    },
    openCreate() {
      this.form = this.emptyForm()
      this.dialogForm = true
    },
    openContribuyenteEdit() {
      this.contribuyenteEdit = this.emptyContribuyente()
      this.contribuyenteEditFotoFile = null
      this.contribuyenteEditFotoPreview = ''
      this.dialogContribuyente = true
    },
    openEdit(row) {
      this.form = {
        id: row.id,
        num_licencia: row.num_licencia || '',
        fecha_licencia: row.fecha_licencia || null,
        vigencia_hasta: row.vigencia_hasta || null,
        tipo: (row.tipo || 'INDEPENDIENTE').toUpperCase(),
        estado: (row.estado || 'ACTIVO').toUpperCase(),
        sindicato_id: row.sindicato_id || row.sindicato?.id || null,
        taxi: {
          placa: row.taxi?.placa || '',
          marca: row.taxi?.marca || '',
          modelo: row.taxi?.modelo || '',
          linea: row.taxi?.linea || '',
          color: row.taxi?.color || '',
          anio: row.taxi?.anio || '',
          chasis: row.taxi?.chasis || '',
          ruat: row.taxi?.ruat || ''
        },
        propietario: {
          cedula: row.taxi?.propietario?.cedula || '',
          comp: row.taxi?.propietario?.comp || '',
          nombre: row.taxi?.propietario?.nombre || '',
          apellido: row.taxi?.propietario?.apellido || '',
          fecha_nacimiento: row.taxi?.propietario?.fecha_nacimiento || null,
          telefono: row.taxi?.propietario?.telefono || '',
          direccion: row.taxi?.propietario?.direccion || '',
          categoria: row.taxi?.propietario?.categoria || ''
        },
        chofer: {
          cedula: row.chofer?.cedula || '',
          comp: row.chofer?.comp || '',
          nombre: row.chofer?.nombre || '',
          apellido: row.chofer?.apellido || '',
          fecha_nacimiento: row.chofer?.fecha_nacimiento || null,
          telefono: row.chofer?.telefono || '',
          direccion: row.chofer?.direccion || '',
          categoria: row.chofer?.categoria || ''
        }
      }
      this.dialogForm = true
    },
    submitForm() {
      this.$q.loading.show()
      const payload = { ...this.form }
      payload.tipo = (payload.tipo || '').toUpperCase()
      if (payload.tipo !== 'SINDICATO') {
        payload.sindicato_id = null
      }

      const request = payload.id
        ? this.$api.put(`licencia/${payload.id}`, payload)
        : this.$api.post('licencia', payload)

      request
        .then(() => {
          this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Guardado correctamente' })
          this.dialogForm = false
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'Error al guardar'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    confirmAnular(row) {
      this.$q.dialog({
        title: 'ANULAR LICENCIA',
        message: `¿Seguro de anular la licencia ${row.num_licencia}?`,
        cancel: true,
        persistent: true
      }).onOk(() => this.anular(row))
    },
    confirmRenovar(row) {
      this.$q.dialog({
        title: 'RENOVAR LICENCIA',
        message: `¿Renovar la licencia ${row.num_licencia}? Solo se permite si esta vencida. Se actualizara la vigencia por 1 ano desde hoy.`,
        cancel: true,
        persistent: true
      }).onOk(() => this.renovar(row))
    },
    renovar(row) {
      this.$q.loading.show()
      this.$api
        .post(`licencia/${row.id}/renovar`)
        .then(() => {
          this.$q.notify({ color: 'teal', textColor: 'white', icon: 'autorenew', message: 'Licencia renovada' })
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'No se pudo renovar'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    anular(row) {
      this.$q.loading.show()
      this.$api
        .post(`licencia/${row.id}/anular`)
        .then(() => {
          this.$q.notify({ color: 'orange', textColor: 'white', icon: 'block', message: 'Licencia anulada' })
          this.loadAll()
        })
        .finally(() => this.$q.loading.hide())
    },
    downloadCredencial(row) {
      if (!this.isVigente(row)) {
        this.$q.notify({ message: 'La credencial solo se imprime si la licencia esta vigente', icon: 'error', color: 'red' })
        return
      }
      this.downloadBlob(`licencia/${row.id}/credencial`, `credencial_${row.num_licencia}.pdf`)
    },
    downloadPdf(row) {
      this.downloadBlob(`licencia/${row.id}/pdf`, `licencia_${row.num_licencia}.pdf`)
    },
    downloadListaPdf(filtro) {
      const f = (filtro || 'todos').toString().toLowerCase()
      const hoy = date.formatDate(new Date(), 'YYYYMMDD')
      this.downloadBlob(`licencias/pdf?filtro=${encodeURIComponent(f)}`, `licencias_${f}_${hoy}.pdf`)
    },
    downloadBlob(endpoint, filename) {
      this.$q.loading.show()
      this.$api
        .get(endpoint, { responseType: 'blob' })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]))
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', filename)
          document.body.appendChild(link)
          link.click()
          link.remove()
          window.URL.revokeObjectURL(url)
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'No se pudo descargar el PDF'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    estadoLabel(row) {
      const estado = (row?.estado || 'ACTIVO').toString().toUpperCase()
      if (estado === 'VIGENTE') return 'ACTIVO'
      if (estado === 'ANULADA') return 'ANULADO'
      return estado || 'ACTIVO'
    },
    estadoColor(row) {
      const e = this.estadoLabel(row)
      if (e === 'ANULADO') return 'red'
      if (e === 'VENCIDO') return 'orange'
      return 'green'
    },
    isVigente(row) {
      const hoy = date.formatDate(new Date(), 'YYYY-MM-DD')
      const e = this.estadoLabel(row)
      if (e === 'ANULADO') return false
      if (row?.vigencia_hasta && row.vigencia_hasta < hoy) return false
      return e === 'ACTIVO'
    },
    isVencida(row) {
      const hoy = date.formatDate(new Date(), 'YYYY-MM-DD')
      const e = this.estadoLabel(row)
      if (e === 'ANULADO') return false
      if (row?.vigencia_hasta && row.vigencia_hasta < hoy) return true
      return e === 'VENCIDO'
    },
    onTipoChange(val) {
      const tipo = (val || '').toUpperCase()
      if (tipo !== 'SINDICATO') {
        this.form.sindicato_id = null
      }
    },
    lookupContribuyente(kind) {
      const obj = this.form?.[kind]
      if (!obj) return
      const cedula = (obj.cedula || '').toString().trim()
      const comp = (obj.comp || '').toString().trim()
      if (!cedula) return

      this.$api
        .get('contribuyente/buscar', { params: { cedula, comp } })
        .then((res) => {
          const data = res?.data?.data
          if (!data) return
          this.form[kind] = {
            cedula: data.cedula || cedula,
            comp: data.comp || comp,
            nombre: data.nombre || '',
            apellido: data.apellido || '',
            fecha_nacimiento: data.fecha_nacimiento || null,
            telefono: data.telefono || '',
            direccion: data.direccion || '',
            categoria: data.categoria || ''
          }
        })
        .catch(() => {
          this.$q.notify({ message: 'Contribuyente no encontrado', icon: 'error', color: 'red' })
        })
    },
    lookupContribuyenteEdit() {
      const cedula = (this.contribuyenteEdit.cedula || '').toString().trim()
      const comp = (this.contribuyenteEdit.comp || '').toString().trim()
      if (!cedula) return

      this.$api
        .get('contribuyente/buscar', { params: { cedula, comp } })
        .then((res) => {
          const data = res?.data?.data
          if (!data) return
          this.contribuyenteEdit = {
            cedula: data.cedula || cedula,
            comp: data.comp || comp,
            nombre: data.nombre || '',
            apellido: data.apellido || '',
            fecha_nacimiento: data.fecha_nacimiento || null,
            telefono: data.telefono || '',
            direccion: data.direccion || '',
            categoria: data.categoria || '',
            foto_url: data.foto_url || null
          }
          this.contribuyenteEditFotoFile = null
          this.contribuyenteEditFotoPreview = data.foto_url || ''
        })
        .catch(() => {
          this.$q.notify({ message: 'Contribuyente no encontrado', icon: 'error', color: 'red' })
        })
    },
    onContribuyenteFotoChange(file) {
      if (this.contribuyenteEditFotoPreview && this.contribuyenteEditFotoPreview.startsWith('blob:')) {
        window.URL.revokeObjectURL(this.contribuyenteEditFotoPreview)
      }

      if (file) {
        this.contribuyenteEditFotoPreview = window.URL.createObjectURL(file)
      } else {
        this.contribuyenteEditFotoPreview = this.contribuyenteEdit?.foto_url || ''
      }
    },
    saveContribuyente() {
      this.$q.loading.show()

      const formData = new FormData()
      formData.append('cedula', (this.contribuyenteEdit.cedula || '').toString().trim())
      formData.append('comp', (this.contribuyenteEdit.comp || '').toString().trim())
      formData.append('nombre', (this.contribuyenteEdit.nombre || '').toString())
      formData.append('apellido', (this.contribuyenteEdit.apellido || '').toString())
      if (this.contribuyenteEdit.fecha_nacimiento) {
        formData.append('fecha_nacimiento', this.contribuyenteEdit.fecha_nacimiento)
      }
      formData.append('telefono', (this.contribuyenteEdit.telefono || '').toString())
      formData.append('direccion', (this.contribuyenteEdit.direccion || '').toString())
      formData.append('categoria', (this.contribuyenteEdit.categoria || '').toString())
      if (this.contribuyenteEditFotoFile) {
        formData.append('foto', this.contribuyenteEditFotoFile)
      }

      this.$api
        .post('contribuyente/actualizar', formData)
        .then(() => {
          this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Contribuyente guardado' })
          this.dialogContribuyente = false
          this.contribuyenteEditFotoFile = null
          if (this.contribuyenteEditFotoPreview && this.contribuyenteEditFotoPreview.startsWith('blob:')) {
            window.URL.revokeObjectURL(this.contribuyenteEditFotoPreview)
          }
          this.contribuyenteEditFotoPreview = ''
          // refresca tabla por si afecta nombres mostrados
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'Error al guardar contribuyente'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    }
  }
}
</script>
