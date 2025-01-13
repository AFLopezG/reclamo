<template>
    <q-page padding>
        <div class="row">
            <div class="col-4 q-pa-xs"><q-input dense outlined  v-model="report.ini" type="date" label="Fecha Ini" /></div>
            <div class="col-4 q-pa-xs"><q-input dense outlined v-model="report.fin" type="date" label="Fecha Fin" /></div>
            <div class="col-4 q-pa-xs"><q-btn color="primary" icon="search" @click="getFormulario" /></div>
        </div>
        <q-table
            title="Listado Reclamos"
            :rows="listado"
            :columns="columns"
            row-key="name"
        >
        <template v-slot:top-right>
          <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
        <template v-slot:body-cell-op="props">
            <q-td key="op" :props="props">
                    <q-btn color="info" icon="print" dense  @click="impresion(props.row.id)"/>
            </q-td>
        </template>

        <template v-slot:body-cell-imagen="props">
            <q-td key="imagen" :props="props">
                    <q-btn color="cyan" icon="image" dense @click="verImagen(props.row.imagen)" v-if="props.row.imagen"/>
            </q-td>
        </template>
    </q-table>
    </q-page>
</template>
<script>
import { date } from 'quasar';

export default {
    name:'reportePage',
    data() {
        return {
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
                {label:'DIRECCION',name:'direccion',field:'direccion'},
                {label:'DESCRIPCION',name:'descripcion',field:'descripcion'},
            ]
        }
    },
    created(){
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
            }).catch(error=>{
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