<template>
    <q-page class="admin-page">
      <div class="admin-hero q-pa-md q-mb-md">
        <div class="row items-center q-col-gutter-md">
          <div class="col-12 col-md-6">
            <div class="text-h5 text-weight-bold">Reporte</div>
            <div class="text-caption admin-subtitle">Listado de reclamos por rango de fechas</div>
          </div>
          <div class="col-12 col-md-6 admin-actions">
            <q-input dense outlined v-model="report.ini" type="date" label="Fecha ini" />
            <q-input dense outlined v-model="report.fin" type="date" label="Fecha fin" />
            <q-btn color="primary" icon="search" label="Buscar" @click="getFormulario" />
          </div>
        </div>
      </div>

      <q-card flat bordered class="admin-card">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-subtitle1 text-weight-medium">Listado</div>
          <q-space />
          <q-input outlined dense debounce="300" v-model="filter" class="admin-search" placeholder="Buscar">
            <template v-slot:append><q-icon name="search" /></template>
          </q-input>
        </q-card-section>
        <q-separator />
        <q-card-section class="q-pa-none">
          <q-table dense flat :rows="listado" :columns="columns" row-key="name" :filter="filter">
            <template v-slot:body-cell-op="props">
              <q-td key="op" :props="props">
                <q-btn color="info" icon="print" dense @click="impresion(props.row.id)" />
              </q-td>
            </template>

            <template v-slot:body-cell-imagen="props">
              <q-td key="imagen" :props="props">
                <q-btn color="cyan" icon="image" dense @click="verImagen(props.row.imagen)" v-if="props.row.imagen" />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-page>
</template>
<script>
import { date } from 'quasar';

export default {
    name:'reportePage',
    data() {
        return {
            store: this.$store,
            filter:'',
            listado:[],
            report:{ini: date.formatDate(new Date(), 'YYYY-MM-DD'),fin: date.formatDate(new Date(), 'YYYY-MM-DD')},
            columns:[
                {label:'OP',name:'op',field:'op'},
                {label:'FECHA',name:'fecha',field:'fecha'},
                {label:'CEDULA',name:'cedula',field:'cedula'},
                {label:'NOMBRE',name:'nombre',field:'nombre'},
                {label:'TELEFONO',name:'telefono',field:'telefono'},
                {label:'IMAGEN',name:'imagen',field:'imagen'},
                {label:'PLACA',name:'placa',field:'placa'},
                {label:'DIRECCION',name:'direccion',field:'direccion'},
                {label:'DESCRIPCION',name:'descripcion',field:'descripcion'},
            ]
        }
    },
    created(){
        if (!this.$store.hasPerm('reporte.ver')) {
          this.$router.replace('/navegador')
          return
        }
        this.getFormulario()
    },
    methods:{
        impresion(r){
            this.$api.get('generatePdf/'+r, {responseType: 'blob'}).then(res=>{
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte_'+r+'.pdf'); // Nombre del archivo
                document.body.appendChild(link);
                link.click();  
            }).catch(()=>{
                alert('error');
            })
                
        },
        getFormulario(){
            this.$api.post('reportFecha',this.report).then(res=>{
                console.log(res.data)
                this.listado=res.data
            })
        },
        verImagen(v){
            this.$q.dialog({
          title: 'Imagen ',
          message: '<img src="'+this.$url+'../imagenes/'+v+'" width="300px" height="300px">',
          html: true
        }).onOk(() => {
          // console.log('OK')                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
        }).onCancel(() => {
          // console.log('Cancel')
        }).onDismiss(() => {
          // console.log('I am triggered on both OK and Cancel')
        })
        }
    }
}
</Script>
