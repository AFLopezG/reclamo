<template>
  <q-page class="admin-page">
    <div class="admin-hero q-pa-md q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12">
          <div class="text-h5 text-weight-bold">Panel</div>
          <div class="text-caption admin-subtitle">Accesos rápidos según tus permisos</div>
        </div>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-4" v-if="$store.hasPerm('usuarios.ver')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/usuario')">
          <q-card-section class="row items-center">
            <q-icon name="people" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Usuarios</div>
              <div class="text-caption admin-subtitle">Administrar cuentas</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4" v-if="$store.hasPerm('licencias.ver')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/licencias')">
          <q-card-section class="row items-center">
            <q-icon name="badge" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Licencias</div>
              <div class="text-caption admin-subtitle">Registro y verificación</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4" v-if="$store.hasPerm('inspeccion.ver')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/inspeccion')">
          <q-card-section class="row items-center">
            <q-icon name="airport_shuttle" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Inspección</div>
              <div class="text-caption admin-subtitle">Inspección vehicular</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4" v-if="$store.hasPerm('reporte.ver')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/reporte')">
          <q-card-section class="row items-center">
            <q-icon name="summarize" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Reporte</div>
              <div class="text-caption admin-subtitle">Reclamos registrados</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4" v-if="$store.hasPerm('posicion.ver')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/posicion')">
          <q-card-section class="row items-center">
            <q-icon name="map" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Posición</div>
              <div class="text-caption admin-subtitle">Personal en mapa</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4" v-if="$store.hasPerm('roles.editar')">
        <q-card flat bordered class="admin-card clickable" @click="$router.push('/roles')">
          <q-card-section class="row items-center">
            <q-icon name="admin_panel_settings" size="32px" />
            <div class="q-ml-md">
              <div class="text-subtitle1 text-weight-medium">Roles y permisos</div>
              <div class="text-caption admin-subtitle">Accesos por rol</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>

export default {
  name: 'IndexPage',
  data() {
        return {
            
        }
    },
};
</script>

<style scoped>
.clickable {
  cursor: pointer;
}
</style>
