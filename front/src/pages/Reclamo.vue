<template>
    <q-layout view="lHh Lpr lFf" class="bg-grey-2">
      <q-page-container>
        <q-page>
          <div class="row">
            <div class="col-12 q-pa-lg">
              <div class="text-h4 text-center q-pa-xs text-black text-bold">RECLAMO ATM   </div>
            <q-card class="my-card">
      <q-card-section class="bg-primary text-white">
        <div class="text-h6">FORMULARIO</div>
      </q-card-section>

      <q-separator />
        <q-form @submit="onSubmit" class="q-gutter-md">
      <!-- Datos de Persona -->
       <div class="row">
        <div class="col-8 q-pa-xs"><q-input outlined v-model="persona.cedula" label="Cédula" @update:model-value="getPersona()" type="number" required/></div>
        <div class="col-4 q-pa-xs"><q-input outlined v-model="persona.comp" label="Comp" @update:model-value="getPersona()" /></div>
        <div class="col-12 q-pa-xs"><q-input outlined v-model="persona.nombre" label="Nombre" required/></div>
        <div class="col-12 q-pa-xs"><q-input outlined v-model="persona.telefono" label="Teléfono" type="number" required/></div>
       </div>


      <!-- Formulario Adicional -->
      <div class="q-pa-xs"><q-input outlined v-model="formulario.direccion" label="Ubicacion" required/></div>
      <div class="q-pa-xs"><q-input outlined v-model="formulario.descripcion" label="Descripción" required/></div>

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
      <div class="col-12"><q-btn label="Enviar" type="submit" icon="send" color="green" class="full-width" /></div>
    </q-form>

  </q-card></div>
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
            formulario:{imagen:null},
        }
    },
    methods:{
      onFileAdded(files) {
        if (files && files.length > 0) {
          this.formulario.imagen = files[0]; // Guarda el primer archivo en la variable
        }
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

      onSubmit(){
       // console.log(this.formulario)
        //return false
        if(this.formulario.imagen==null)
          return false
        this.$q.loading.show()
        const formData = new FormData();
        formData.append('cedula', this.persona.cedula);
        formData.append('comp', this.persona.comp);
        formData.append('nombre', this.persona.nombre);
        formData.append('telefono', this.persona.telefono);
        formData.append('direccion', this.formulario.direccion);
        formData.append('descripcion', this.formulario.descripcion);
        if (this.formulario.imagen) {
          formData.append('imagen', this.formulario.imagen);
        }

        this.$api.post('formulario',formData).then(()=>{
          this.persona={}          
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
</style>