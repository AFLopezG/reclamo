<template>
    <q-page class="admin-page">
      <div class="admin-hero q-pa-md q-mb-md">
        <div class="row items-center q-col-gutter-md">
          <div class="col-12 col-md-6">
            <div class="text-h5 text-weight-bold">Usuarios</div>
            <div class="text-caption admin-subtitle">Gestión de cuentas del sistema</div>
          </div>
          <div class="col-12 col-md-6 admin-actions">
            <q-input outlined dense debounce="300" v-model="filter" class="admin-search" placeholder="Buscar usuario">
              <template v-slot:append><q-icon name="search" /></template>
            </q-input>
            <q-btn v-if="$store.hasPerm('usuarios.editar')" label="Nuevo usuario" color="positive" @click="regDialog" icon="add_circle" />
          </div>
        </div>
      </div>

      <q-dialog v-model="alert" full-width>
        <q-card >
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7"><q-icon name="add_circle" /> REGISTRO DE NUEVO USUARIO</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
              <div class="row">
                <div class="col-12"><q-input outlined v-model="dato.nombre"   type="text" label="Nombre " hint="Ingresar Nombre" dense lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']"/></div>
                <div class="col-12"><q-select outlined v-model="rol" :options="roles" label="Rol " option-label="nombre" dense /></div>
                <div class="col-12"><q-input outlined dense v-model="dato.email" type="email" label="Email" hint="Correo electronico" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" /></div>
                <div class="col-12"><q-input outlined dense v-model="dato.name" label="Cuenta" hint="Cuenta" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" /></div>
                <div class="col-12"><q-input outlined dense v-model="dato.password" label="Contraseña" hint="Contraseña" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" :type="typePassword?'password':'text'">
                  <template v-slot:append>
                            <q-icon @click="typePassword=!typePassword" :name="typePassword?'visibility':'visibility_off'" />
                  </template>
                </q-input>
              </div>
            </div>


              <div>
                <q-btn label="Crear" type="submit" color="positive" icon="add_circle" />
                <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-card flat bordered class="admin-card">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-subtitle1 text-weight-medium">Listado</div>
          <q-space />
          <q-badge color="grey-8" :label="`${data.length} registros`" />
        </q-card-section>
        <q-separator />
        <q-card-section class="q-pa-none">
          <q-table dense flat :filter="filter" :rows="data" :columns="columns" row-key="name" :rows-per-page-options="[50,100]">

           <template v-slot:body-cell-estado="props">
            <q-td key="estado" :props="props">
              <q-badge :color="props.row.estado=='ACTIVO'?'green':'red'"  :label="props.row.estado" @click="cambioEstado(props.row)" />
            </q-td>
        </template>
            <template v-slot:body-cell-opcion="props">

            <q-td key="opcion" :props="props">
              <q-btn v-if="$store.hasPerm('usuarios.editar')" dense round flat color="yellow" @click="editRow(props)" icon="edit" />
              <q-btn v-if="$store.hasPerm('usuarios.editar')" dense round flat color="positive" @click="cambiopass(props)" icon="vpn_key" />
              <q-btn v-if="$store.hasPerm('usuarios.editar')" dense round flat color="red" @click="deleteRow(props)" icon="delete" ></q-btn>
            </q-td>

        </template>
      </q-table>
        </q-card-section>
      </q-card>

      <q-dialog v-model="dialog_mod">
        <q-card style="max-width: 80%; width: 50%">
          <q-card-section class="bg-warning text-white">
            <div class="text-h7"> <q-icon name="edit"/> MODIFICAR DATOS DE USUARIO</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onMod" class="q-gutter-md">
              <q-input outlined dense v-model="dato2.nombre" type="text" label="Nombre "  hint="Ingresar Nombre" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" />
              <q-input outlined dense v-model="dato2.name" type="text" label="Cuenta "  hint="Ingresar cuenta" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" />
              <q-input outlined dense v-model="dato2.email" type="text" label="Correo "  hint="Ingresar Correo" lazy-rules :rules="[(val) => val.length > 0 || 'Por favor ingresa datos']" />
              <div>
                <q-btn label="Modificar" type="submit" color="positive" icon="add_circle" />
                <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_del">
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="clear" color="red" text-color="white" />
            <span class="q-ml-sm">Seguro de eliminar Registro.</span>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Eliminar" color="deep-orange" @click="onDel" />
            <q-btn flat label="Cancelar" color="primary" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>


    </q-page>
  </template>

  <script>
  import { date } from 'quasar'
  import {globalStore} from   '../stores/globalStore'

  export default {
    name: 'UserPage',
    data () {
      return {
        store:globalStore(),
        //roles: ['ADMINISTRADOR', 'USUARIO', 'INSPECTOR','CHOFER'],
        roles: [],
        rol:{nombre:''  },
        alert: false,
        dialog_mod: false,
        dialog_del: false,
        typePassword: true,
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
        filter: '',
        dato: {  },
        model: '',
        dato2: {},
        options: [],
        props: [],
        filterU:[],
        uni: {},
        columns: [
          { name: 'opcion', label: 'OPCIÓN', field: 'action', sortable: false },
          { name: 'name', align: 'left', label: 'NOMBRE ', field: 'name', sortable: true },
          { name: 'rol', align: 'left', label: 'ROL ', field: 'rol', sortable: true },
          { name: 'email', align: 'left', label: 'E-MAIL', field: 'email', sortable: true },
          { name: 'estado', align: 'left', label: 'ESTADO', field: 'state', sortable: true },
        ],
        data: []
      }
    },

    mounted () {
      if (!this.$store.hasPerm('usuarios.ver')) {
        this.$router.replace({ path: '/' })
      }

      this.misdatos()
      this.getRoles()
    },
    methods: {
      getRoles () {
        this.$api.get('rol').then(res => {
          this.roles = res.data
        })
      },
      regDialog () {
        this.dato = { }
        this.alert = true
      },

      misdatos () {
        this.$q.loading.show()
        this.$api.get('user').then((res) => {
          console.log(res.data)
          this.data = res.data
          this.$q.loading.hide()
        })
      },
      editRow (item) {
        this.dato2 = item.row
        this.dialog_mod = true
      },
      deleteRow (item) {
        this.dato2 = item.row
        this.dialog_del = true
      },
      onSubmit () {

        this.$q.loading.show()
        if(!this.rol || !this.rol.id){
          this.$q.notify({
            message: 'Selecciona un rol',
            color: 'red',
            icon: 'error'
          })
          this.$q.loading.hide()
          return
        }
        this.dato.rol_id = this.rol.id
        this.$api.post('user', this.dato).then(() => {
          // console.log(res.data)
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Creado correctamente'
          })
          this.dato = {  }
          this.alert = false
          this.misdatos()
        }).catch(err => {
          console.log(err.response.data)
          this.$q.notify({
            message: err.response.data.message,
            icon: 'close',
            color: 'red'
          })
          this.$q.loading.hide()
        })
      },
      onMod () {
 
        this.$q.loading.show()
        if(!this.rol || !this.rol.id){
          this.$q.notify({
            message: 'Selecciona un rol',
            color: 'red',
            icon: 'error'
          })
          this.$q.loading.hide()
          return
        }
        this.dato2.rol_id = this.rol.id
        this.$api.put('user/' + this.dato2.id, this.dato2).then(() => {
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Modificado correctamente'
          })
          this.dialog_mod = false
          this.misdatos()
        })
      },
      onDel () {
        this.$q.loading.show()
        this.$api.delete('user/' + this.dato2.id)
          .then(() => {
            this.$q.notify({
              color: 'green-4',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Eliminado correctamente'
            })
            this.dialog_del = false
            this.misdatos()
          }).catch(err => {
            this.$q.loading.hide()
            this.$q.notify({
              message: err.response.data.message,
              icon: 'error',
              color: 'red'
            })
          })
      },
      onReset () {
        this.dato.nombre = null
        this.dato.inicio = 0
        this.dato.fin = 0
      },
      cambioEstado(user1){
        this.$api.post('cambioEstado/' + user1.id).then(() => {
          this.misdatos()
        })

      },
      cambiopass (i) {
        // console.log(i.row);
        this.$q.dialog({
          title: 'CAMBIAR PASSWORD',
          message: 'Ingresar nueva contraseña',
          prompt: {
            model: '',
            type: 'text' // optional
          },
          cancel: true,
          persistent: true
        }).onOk(data => {
          this.$q.loading.show()
          this.$api.put('updatePassword/' + i.row.id, { password: data }).then(() => {
            this.$q.loading.hide()
          })
        }).onCancel().onDismiss()
      }
    }
  }
  </script>
