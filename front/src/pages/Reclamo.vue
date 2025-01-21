<template>
    <q-layout view="lHh Lpr lFf" >
      <q-page-container class="dynamic-bg q-pa-sm">
        <q-page>
          <br><br>
          <br><br>
          <br><br>
          <br>
          <br><br>
          <div class="row">
            <div class="col-12 q-pa-lg">


      <q-separator />
        <q-form @submit="onSubmit" class="q-gutter-md">
      <!-- Datos de Persona -->
       <div class="row">
        <div class="col-12 q-pa-xs"><q-input bg-color="white" dense rounded outlined v-model="persona.cedula" label="Cédula" @update:model-value="getPersona()" type="number" required/></div>
        <!--<div class="col-4 q-pa-xs"><q-input bg-color="white" rounded outlined v-model="persona.comp" label="Comp" @update:model-value="getPersona()" /></div>-->
        <div class="col-12 q-pa-xs"><q-input bg-color="white" dense rounded outlined v-model="persona.nombre" label="Nombre" required/></div>
        <div class="col-12 q-pa-xs"><q-input bg-color="white" dense  rounded outlined v-model="persona.telefono" label="Teléfono" type="number" required/></div>

        <div class="col-12 q-pa-xs"><q-input bg-color="white" dense  rounded outlined v-model="vehiculo.placa" type="text" label="PLACA" required/></div>
        <div class="col-12 q-pa-xs"><q-select bg-color="white" dense  rounded outlined v-model="delito" :options="delitos" label="Infraccion"  option-label="detalle"/></div>
        <!--<div class="col-6"><q-select v-model="vehiculo.tipo" :options="['Taxi','Mini/Trufi','Microbus']" label="Tipo" filled /></div>-->
      <!-- Formulario Adicional -->
      <div class="col-12 q-pa-xs"><q-input bg-color="white" dense  rounded outlined v-model="formulario.direccion" label="Ubicacion" required/></div>
      <div class="col-12 q-pa-xs"><q-input bg-color="white" dense  rounded outlined v-model="formulario.descripcion" label="Descripción" required/></div>
    </div>

      <!-- Cargar Archivos -->
       <div align="center">       
        <q-uploader
        label="Cargar Imagen"
        v-model="formulario.imagen"
        accept="image/*"
        @added="onFileAdded"
        :max-file-size="2000000"
        ref="uploader"
        :rules="[files => files.length > 0 || 'La imagen es requerida']"
      />
      </div>
      <!-- Previsualización -->
      <div v-if="formulario.imagen"> 
        <q-img :src="formulario.imagen" class="q-mb-md" :style="{ maxWidth: '200px' }" />
      </div>

      <!-- Botón de Enviar -->
      <div class="col-12 text-center"><q-btn dense  rounded label="Enviar su Reclamo" type="submit" icon="send" color="green"  size="15px"/></div>
    </q-form>

  </div>
</div>
</q-page>
      </q-page-container>
    </q-layout>   
</template>
<script>

export default {
    name:'reclamoPage',
    data() {
        return {
            persona:{},
            vehiculo:{},
            delito:{detalle:''},
            delitos:[],
            formulario:{imagen:null},
        }
    },
    mounted(){
      this.getDelitos()
    },
    methods:{
      onFileAdded(files) {
        if (files && files.length > 0) {
          this.formulario.imagen = files[0]; // Guarda el primer archivo en la variable
        }
      },  
      getDelitos(){
        this.$api.get('delito').then(res=>{
          console.log(res.data)
          this.delitos=res.data
        })
      },
      getPersona(){
        if((this.persona.cedula).length > 4){
        this.$api.post('searchpersona',this.persona).then(res=>{
          console.log(res.data)
          if(res.data.success){
              this.persona.nombre=res.data.persona.nombre
              this.persona.telefono=res.data.persona.telefono
              
          }
          else{
            this.persona.nombre=''
            this.persona.telefono='' 
          }
        })
        }
      },

      getVehiculo(){
        if((this.vehiculo.placa).length > 4){
        this.$api.post('searchvehiculo',this.vehiculo).then(res=>{
          console.log(res.data)
          if(res.data.success){
              this.vehiculo.tipo=res.data.vehiculo.tipo
              this.vehiculo.linea=res.data.vehiculo.linea
              this.vehiculo.numero=res.data.vehiculo.numero
              this.vehiculo.carnet=res.data.vehiculo.carnet
              this.vehiculo.chofer=res.data.vehiculo.chofer
          }
          else{
            this.vehiculo.tipo=''
              this.vehiculo.linea=''
              this.vehiculo.numero=''
              this.vehiculo.carnet=''
              this.vehiculo.chofer=''
          }
        })
        }
      },

      onSubmit(){
       // console.log(this.formulario)
        //return false
        if(this.formulario.imagen==null){
          this.$q.notify({
              message: 'Debe subir imagen/foto.',
              color: 'red',
              icon:'info'
            })
          return false
        }
        if(this.delito.id==undefined)
          {
            this.$q.notify({
              message: 'Debe seleccionar Infraccion.',
              color: 'red',
              icon:'info'
            })
            return false
          }
        this.$q.loading.show()
        const formData = new FormData();
        formData.append('cedula', this.persona.cedula);
        formData.append('comp', this.persona.comp);
        formData.append('nombre', this.persona.nombre);
        formData.append('telefono', this.persona.telefono);
        formData.append('direccion', this.formulario.direccion);
        formData.append('descripcion', this.formulario.descripcion);
        formData.append('placa', this.vehiculo.placa);
        formData.append('delito_id', this.delito.id);
        if (this.formulario.imagen) {
          formData.append('imagen', this.formulario.imagen);
        }

        this.$api.post('formulario',formData).then(()=>{
          this.persona={}          
          this.vehiculo={}          
          this.delito={detalle:''}
          this.$refs.uploader.removeFile(this.formulario.imagen); 
          this.formulario={imagen:null}
          this.$q.notify({
          message: 'Registado reclamo',
          color: 'green',
          icon:'check_circle_outline'
        })
        }).catch(error=> {
          this.$q.notify({
          message: error.message,
          color: 'red',
          icon:'info'
        })
        }).finally(() => this.$q.loading.hide())

        return false;
      }
    }
}
</script>
<style scoped>
  .inputPrice >>> input[type="number"] {
  -moz-appearance: textfield;
    }
    .inputPrice >>> input::-webkit-outer-spin-button,
    .inputPrice >>> input::-webkit-inner-spin-button {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
    }
  .bg-small {
    background-image: url('/img/movil.png');
    background-size: contain;
    background-position: center;
  }

  .bg-large {
    background-image: url('/img/pc.png');
    background-size: 10%;
    background-position: center;
  }

  @media (min-width: 768px) {
    /* Aplica este estilo para pantallas más grandes */
    .dynamic-bg {
      background-repeat: no-repeat;
  background-size: 100% 100%;
  background-image: url('/img/pc.png');
    }
  }

  @media (max-width: 767px) {
    /* Aplica este estilo para pantallas pequeñas */
    .dynamic-bg {
  background-repeat: no-repeat;
  background-size: 100% 100%;
      background-image: url('/img/movil.png');
    }
  }
</style>