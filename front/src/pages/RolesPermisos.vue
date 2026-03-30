<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <div class="text-h5 text-weight-bold">Roles y Permisos</div>
          <div class="text-caption admin-subtitle">Crear roles y asignar permisos por menú</div>
        </div>
        <div class="col-12 col-md-6 admin-actions">
          <q-btn color="positive" icon="add_circle" label="Nuevo rol" @click="openNewRole" />
        </div>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-4">
        <q-card flat bordered class="admin-card">
          <q-card-section class="row items-center q-pb-sm">
            <div class="text-subtitle1 text-weight-medium">Roles</div>
            <q-space />
            <q-badge color="grey-8" :label="`${roles.length}`" />
          </q-card-section>
          <q-separator />
          <q-card-section class="q-pa-none">
            <q-list separator>
              <q-item
                v-for="r in roles"
                :key="r.id"
                clickable
                :active="selectedRole && selectedRole.id === r.id"
                active-class="bg-primary text-white"
                @click="selectRole(r)"
              >
                <q-item-section>
                  <q-item-label>{{ r.nombre }}</q-item-label>
                  <q-item-label caption>{{ r.descripcion || '' }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <div class="row">
                    <q-btn dense round flat icon="edit" @click.stop="openEditRole(r)" />
                    <q-btn dense round flat icon="delete" color="red" @click.stop="confirmDeleteRole(r)" />
                  </div>

                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-8">
        <q-card flat bordered class="admin-card">
          <q-card-section class="row items-center q-pb-sm">
            <div class="text-subtitle1 text-weight-medium">Permisos</div>
            <q-space />
            <q-badge color="grey-8" :label="selectedRole ? selectedRole.nombre : 'Seleccione un rol'" />
          </q-card-section>
          <q-separator />
          <q-card-section v-if="!selectedRole">
            <div class="text-grey-7">Seleccione un rol para editar sus permisos.</div>
          </q-card-section>
          <q-card-section v-else>
            <q-input dense outlined v-model="permFilter" placeholder="Filtrar permisos (código o nombre)">
              <template v-slot:append><q-icon name="search" /></template>
            </q-input>

            <div class="q-mt-md">
              <div v-for="group in permisoGroups" :key="group.menu" class="q-mb-md">
                <div class="text-subtitle2 q-mb-xs">{{ group.menu }}</div>
                <div class="row q-col-gutter-sm">
                  <div class="col-12 col-md-6" v-for="p in group.permisos" :key="p.id">
                    <q-checkbox v-model="selectedPerms" :val="p.id" :label="`${p.nombre} (${p.codigo})`" />
                  </div>
                </div>
              </div>
            </div>

            <div class="row q-mt-md">
              <q-btn color="info" icon="save" label="Guardar permisos" @click="savePermisos" />
              <q-btn flat color="grey-8" label="Recargar" icon="refresh" class="q-ml-sm" @click="reloadRolePerms" />
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-dialog v-model="dialogRole" persistent>
      <q-card style="min-width: 420px; max-width: 95vw">
        <q-card-section class="bg-primary text-white">
          <div class="text-h7">
            <q-icon :name="roleForm.id ? 'edit' : 'add_circle'" /> {{ roleForm.id ? 'Editar rol' : 'Nuevo rol' }}
          </div>
        </q-card-section>
        <q-card-section class="q-pt-sm">
          <q-form @submit="saveRole" class="q-gutter-sm">
            <q-input outlined dense v-model="roleForm.nombre" label="Nombre" :rules="[(v) => !!v || 'Requerido']" />
            <q-input outlined dense v-model="roleForm.descripcion" label="Descripción" />
            <div class="row q-mt-md">
              <q-btn color="positive" icon="save" :label="roleForm.id ? 'Guardar' : 'Crear'" type="submit" />
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
  name: 'RolesPermisosPage',
  data() {
    return {
      roles: [],
      permisos: [],
      asignadosIds: {},
      selectedRole: null,
      selectedPerms: [],
      permFilter: '',
      dialogRole: false,
      roleForm: { id: null, nombre: '', descripcion: '' }
    }
  },
  created() {
    if (!this.$store.hasPerm('roles.editar')) {
      this.$router.replace('/navegador')
      return
    }
    this.loadAll()
  },
  computed: {
    permisoGroups() {
      const filter = (this.permFilter || '').toString().toLowerCase().trim()
      const perms = (this.permisos || []).filter((p) => {
        if (!filter) return true
        return (p.codigo || '').toLowerCase().includes(filter) || (p.nombre || '').toLowerCase().includes(filter) || (p.menu || '').toLowerCase().includes(filter)
      })

      const groups = {}
      perms.forEach((p) => {
        const menu = (p.menu || 'OTROS').toString()
        if (!groups[menu]) groups[menu] = []
        groups[menu].push(p)
      })

      return Object.keys(groups)
        .sort()
        .map((menu) => ({ menu, permisos: groups[menu].sort((a, b) => (a.codigo || '').localeCompare(b.codigo || '')) }))
    }
  },
  methods: {
    normalizePermIds(list) {
      return (Array.isArray(list) ? list : [])
        .map((v) => (typeof v === 'number' ? v : parseInt((v ?? '').toString(), 10)))
        .filter((v) => Number.isFinite(v) && v > 0)
    },
    loadAll() {
      this.$q.loading.show()
      Promise.all([this.$api.get('rol'), this.$api.get('permiso'), this.$api.get('permiso/roles')])
        .then(([rRoles, rPerms, rAsign]) => {
          this.roles = rRoles.data || []
          this.permisos = rPerms.data || []
          this.asignadosIds = rAsign?.data?.asignados_ids || rAsign?.data?.asignadosIds || {}
        })
        .catch(() => {
          this.$q.notify({ message: 'No se pudo cargar roles/permisos', icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    selectRole(role) {
      this.selectedRole = role
      const listIds = this.asignadosIds?.[role.id] || this.asignadosIds?.[String(role.id)] || null
      this.selectedPerms = this.normalizePermIds(listIds)
    },
    reloadRolePerms() {
      if (!this.selectedRole) return
      this.$q.loading.show()
      this.$api
        .get('permiso/roles')
        .then((res) => {
          this.asignadosIds = res?.data?.asignados_ids || res?.data?.asignadosIds || {}
          const listIds = this.asignadosIds?.[this.selectedRole.id] || this.asignadosIds?.[String(this.selectedRole.id)] || null
          this.selectedPerms = this.normalizePermIds(listIds)
        })
        .finally(() => this.$q.loading.hide())
    },
    openNewRole() {
      this.roleForm = { id: null, nombre: '', descripcion: '' }
      this.dialogRole = true
    },
    openEditRole(role) {
      this.roleForm = { id: role.id, nombre: role.nombre, descripcion: role.descripcion || '' }
      this.dialogRole = true
    },
    saveRole() {
      const payload = { nombre: this.roleForm.nombre, descripcion: this.roleForm.descripcion }
      const req = this.roleForm.id ? this.$api.put(`rol/${this.roleForm.id}`, payload) : this.$api.post('rol', payload)
      this.$q.loading.show()
      req
        .then(() => {
          this.$q.notify({ message: 'Rol guardado', icon: 'check', color: 'green' })
          this.dialogRole = false
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'Error al guardar rol'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    confirmDeleteRole(role) {
      this.$q
        .dialog({
          title: 'Eliminar rol',
          message: `¿Eliminar el rol ${role.nombre}?`,
          cancel: true,
          persistent: true
        })
        .onOk(() => this.deleteRole(role))
    },
    deleteRole(role) {
      this.$q.loading.show()
      this.$api
        .delete(`rol/${role.id}`)
        .then(() => {
          this.$q.notify({ message: 'Rol eliminado', icon: 'delete', color: 'green' })
          if (this.selectedRole && this.selectedRole.id === role.id) {
            this.selectedRole = null
            this.selectedPerms = []
          }
          this.loadAll()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'No se pudo eliminar el rol'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    },
    savePermisos() {
      if (!this.selectedRole) return
      this.$q.loading.show()
      this.$api
        .post('permiso/asignar', { rol_id: this.selectedRole.id, permisos: this.normalizePermIds(this.selectedPerms) })
        .then(() => {
          this.$q.notify({ message: 'Permisos guardados', icon: 'check', color: 'green' })
          this.reloadRolePerms()
        })
        .catch((err) => {
          const msg = err?.response?.data?.message || 'No se pudo guardar permisos'
          this.$q.notify({ message: msg, icon: 'error', color: 'red' })
        })
        .finally(() => this.$q.loading.hide())
    }
  }
}
</script>
