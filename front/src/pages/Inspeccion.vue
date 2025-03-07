<template>
    <q-page paddding>
        <div class="text-h5 text-bold text-center">INSPECCION VEHICULAR </div>
        <div class="row">
            <div class="col-4 q-pa-xs"><q-input v-model="fecha" type="date" label="Fecha" outlined dense/></div>
            <div class="col-2 q-pa-xs" ><q-btn color="info" icon="search"  @click="getInspecciones"  dense/></div>
        </div>
        <q-table
            :rows="inspecciones"
            :columns="columns"
            row-key="name"
            :filter="filter"
        >
        <template v-slot:top-right>
            <q-btn color="green" icon="check" label="REGISTRAR" @click="formulario" />
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                <template v-slot:append>
                    <q-icon name="search" />
                </template>
            </q-input>
        </template>
        <template v-slot:body-cell-op="props">
            <q-td key="op" :props="props">
                <q-btn color="info" icon="print" dense @click="impresion(props.row)"/>
            </q-td>
        </template>
    </q-table>

    <q-dialog v-model="dialogRegistro" full-width>
        <q-card>
            <q-card-section class="row items-center">
                <q-avatar icon="car_repair" color="primary" text-color="white" size="sm"/>
                <span class="q-ml-sm">Formulario de Registro Inspecion.</span>
            </q-card-section>
            <q-form
                @submit="onSubmit"
            >                
                <q-card-section>
                    <div class="row">
                        <div class="col-12 text-bold text-h6">DATOS DE PROPIETARIO</div>
                        <div class="col-md-5 col-xs-12 q-pa-xs"><q-input style="text-transform: uppercase;" v-model="propietario.cedula" label="Cedula" outlined dense required @update:model-value="buscaProp" placeholder="99999-XX"/></div>
                        <div class="col-md-5 col-xs-12 q-pa-xs"><q-select v-model="propietario.categoria" label="Categoria" :options="['A','B','C']" outlined dense required/></div>
                        <div class="col-md-2 col-xs-12 q-pa-xs"><q-toggle v-model="propietario.seguro" :label="'Seguro '+ propietario.seguro" color="green" false-value="FOCAT" true-value="SOAT" outlined dense required/></div>
                        <div class="col-md-12 col-xs-12 q-pa-xs"><q-input style="text-transform: uppercase;" v-model="propietario.nombre" label="Nombre" outlined dense required/></div>
                    </div>

                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 text-bold text-h6">DATOS DE VEHICULO</div>
                        <div class="col-md-6 col-xs-12 q-pa-xs"><q-input style="text-transform: uppercase;" v-model="vehiculo.placa" label="Placa" outlined dense required @update:model-value="buscaVehi" placeholder="9999XXX"/></div>
                        <div class="col-md-6 col-xs-12 q-pa-xs"><q-select v-model="vehiculo.tipo" label="Tipo" outlined dense :options="tipos" required/></div>
                        <div class="col-md-6 col-xs-12 q-pa-xs">
                            <q-input style="text-transform: uppercase;" v-model="vehiculo.marca" label="Marca" outlined dense list="marcas-lista" required />
                            <datalist id="marcas-lista">
                                <option v-for="marca in marcas" :key="marca" :value="marca">{{ marca }}</option>
                            </datalist>
                        </div>
                        <div class="col-md-6 col-xs-12 q-pa-xs">
                            <q-input style="text-transform: uppercase;" v-model="vehiculo.modelo" label="Modelo" outlined dense list="modelos-lista" required/>
                            <datalist id="modelos-lista">
                                <option v-for="modelo in modelos" :key="modelo" :value="modelo">{{ modelo }}</option>
                            </datalist>
                        </div>
                        <div class="col-md-6 col-xs-12 q-pa-xs">
                            <q-input style="text-transform: uppercase;" v-model="vehiculo.linea" label="Linea" outlined dense list="lineas-lista"   required/>      
                            <datalist id="lineas-lista">
                                <option v-for="linea in lineas" :key="linea" :value="linea">{{ linea }}</option>
                            </datalist>                  
                        </div>
                    </div>

                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-md-12 text-center text-bold text-h5">DATOS DE INSPECCION</div>
                        <div class="col-md-12 text-bold text-h6">INSPECCION INTENA</div>
                        <div class="col-md-6 col-xs-12" >   
                        <div class=" row" >   
                                <div style="width: 20%;">VENTANAS:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.ventanas" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.ventanas" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.ventanas" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div></div>
                        <hr>  
                        <div class="col-md-6 col-xs-12">   
                        <div class=" row">   
                                <div style="width: 20%;">PUERTAS:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.puertas" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.puertas" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.puertas" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div></div>
                        <hr>  
                        <div class="col-md-6 col-xs-12">   
                        <div class=" row">   
                                <div style="width: 20%;">VENTILACION:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.ventilacion" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.ventilacion" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.ventilacion" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div></div>
                        <hr>  
                        <div class="col-md-6 col-xs-12">   
                        <div class=" row">   
                                <div style="width: 20%;">LUZ INTERIOR:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.luz" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.luz" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.luz" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div></div>
                        <hr>  
                        <div class="col-md-6 col-xs-12">   
                        <div class=" row">   
                                <div style="width: 20%;">HIGIENE:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.higiene" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.higiene" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.higiene" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div></div>
                        <hr>  
                        <div class="col-md-12 text-bold text-h6">EMERGENCIA</div>
                        <div class="col-md-6 col-xs-12">   
                        <div class="row">   
                                <div style="width: 20%;">TRIANGULO:   </div>
                            <div class="q-gutter-sm">
                                <q-radio v-model="inspeccion.triangulo" val="BUENO" label="BUENO" />
                                <q-radio v-model="inspeccion.triangulo" val="MALO" label="MALO" />
                                <q-radio v-model="inspeccion.triangulo" val="NO TIENE" label="NO TIENE" />
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6 col-xs-12 q-pa-xs text-h6"><q-toggle v-model="inspeccion.calificacion" :label="'Resultado : ' + inspeccion.calificacion" color="green" true-value="CUMPLE" false-value="NO CUMPLE" outlined dense required/></div>
                        <div class="col-md-12 col-xs-12 q-pa-xs"><q-input v-model="inspeccion.observacion" label="OBSERVACION" type="textarea" outlined dense/></div>
                        <div class="col-md-6 col-xs-12 q-pa-xs"><q-input v-model="inspeccion.radicatoria" label="Radicatoria" outlined dense/></div>
                    </div>
                </q-card-section>
            <q-card-actions align="right">
                <q-btn flat label="Cancelar" color="red" v-close-popup />
                <q-btn flat label="REGISTRAR" color="green" type="submit" />
            </q-card-actions>
            </q-form>
        </q-card>
    </q-dialog>
    <div id="myelement" class="hidden"></div>

    </q-page>
</template>
<script>
import { date } from 'quasar';
import { Printd } from 'printd'

export default {
    name:'inspeccionPage',
    data() {
        return {
            filter:'',
            fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
            tipos:[],
            marcas:[],
            modelos:[],
            lineas:[],
            inspecciones:[],
            propietario:{},
            vehiculo:{},
            inspeccion:{},
            dialogRegistro:false,
            columns:[
                {label:'op',name:'op',field:'op'},
                {label:'FECHA',name:'fecha',field:'fecha'},
                {label:'CEDULA',name:'cedula',field:row=>row.propietario.cedula},
                {label:'NOMBRE',name:'nombre',field:row=>row.propietario.nombre},
                {label:'PLACA',name:'placa',field:row=>row.vehicle.placa},
                {label:'LINEA',name:'linea',field:row=>row.vehicle.linea},
                {label:'TIPO',name:'tipo',field:row=>row.vehicle.tipo},
                {label:'INSPECTOR',name:'inspector',field:row=>row.user.nombre},
                {label:'CALIFICACION',name:'calificacion',field:'calificacion'},
                {label:'OBSERVACION',name:'observacion',field:'observacion'},
            ]
        }
    },
    created() {
        this.getTipos();
        this.getMarcas();
        this.getModelos();
        this.getLineas();
        this.getInspecciones();
    },
    methods:{
        impresion(dato){
            let cadena=`<style>
            .imag1{
            width: 70px;
            height: 70px;
            }
            .titulo{
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            }
            .subtitulo{
            font-size: 15px;
            font-weight: bold;
            }
            .tab1{
            width: 100%;
            }
            .tab2{
            width: 100%;
            }
            .tab2 th{
            text-align: right;
            }
                        .tab21{
            width: 100%;
            font-size: 18px;
            }
            .tab21 th{
            text-align: left;
            }
            .tab3{
            width: 100%;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            cell-padding: 10px;
            }
            .tab3 td{
                border-top: 1px solid black;
                padding-top: 10px;
            }
            </style>
            <div>
            <table class="tab1">
                <tr><td style="width:15%"><img class="imag1" src="img/escudo.jpg"></td><td class="titulo">FICHA DE CONTROL <br> DE SEGURIDAD Y CALIDAD DE SERVICIO</td><td style="width:15%"></td></tr>
            </table>

            <div class="subtitulo">DATOS GENERALES DEL PROPIETARIO</div>
            <table class="tab2">
                <tr><th>CEDULA:</th><td>${dato.propietario.cedula}</td><th>CATEGORIA:</th><td>${dato.propietario.categoria}</td><th>SEGURO:</td><td>${dato.propietario.seguro}</td></tr>
                <tr><th>NOMBRE:</th><td colspan=5>${dato.propietario.nombre}</td></tr>
            </table>
            <hr>

            <div class="subtitulo">DATOS GENERALES DEL VEHICULO</div>
            <table class="tab2">
                <tr><th>PLACA:</th><td>${dato.vehicle.placa}</td><th>TIPO:</th><td>${dato.vehicle.tipo}</td></tr>
                <tr><th>MARCA:</th><td>${dato.vehicle.marca}</td><th>MODELO:</th><td>${dato.vehicle.modelo}</td></tr>
                <tr><th>LINEA:</th><td>${dato.vehicle.linea}</td></tr>
            </table>
            <hr>
            <div class="subtitulo">INSPECCION INTERNA</div>
            <table class="tab2">
                <tr><th>VENTANAS:</th><td>${dato.ventanas}</td><th>PUERTAS:</th><td>${dato.puertas}</td></tr>
                <tr><th>VENTILACION:</th><td>${dato.ventilacion}</td><th>LUZ INTERIOR:</th><td>${dato.luz}</td></tr>
                <tr><th>HIGIENE:</th><td>${dato.higiene}</td><th>TRIANGULO:</th><td>${dato.triangulo}</td></tr>
            </table>
            <hr>
            <table class="tab21">
                <tr><th style="">CALIFICACION:</th><td style="font-size:25px">${dato.calificacion}</td></tr>
            </table><hr>
            <div class="subtitulo">OBSERVACION</div>
            <div class="tab2">
                <p>${dato.observacion}</p>
            </div>
            <div class="subtitulo">RADICATORIA</div>
            <div class="tab2">
                <p>${dato.radicatoria}</p>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table class="tab3">
                <tr><td style='width:40%'>INSPECTOR<br>${dato.user.nombre}</td><td style="width:20%; border:0"></td><td style='width:40%'>PROPIETARIO<br>${dato.propietario.nombre}</td></tr>
            </table>
            </div>`
            document.getElementById('myelement').innerHTML = cadena
            const d = new Printd()
            d.print( document.getElementById('myelement') ) 
        },
        buscaProp(){
            let carnet=this.propietario.cedula;
            if(carnet.length<4) return;
            this.$api.get('propietario/'+carnet).then(response=>{
                if(response.data){
                this.propietario=response.data;
                }
            })
        },
        buscaVehi(){
            let placa=this.vehiculo.placa;
            if(placa.length<4) return;
            this.$api.get('vehicle/'+placa).then(response=>{
                if(response.data){
                    this.vehiculo=response.data;
                }
            })
        },
        onSubmit(){
            if(this.inspeccion.ventanas==undefined || this.inspeccion.puertas==undefined || this.inspeccion.ventilacion==undefined || this.inspeccion.luz==undefined || this.inspeccion.higiene==undefined || this.inspeccion.triangulo==undefined || this.inspeccion.calificacion==undefined){
                this.$q.notify({
                    color: 'red',
                    position: 'top',
                    message: 'Faltan datos por completar',
                    icon: 'report_problem'
                })
                return;
            }
            console.log(this.inspeccion)
            this.inspeccion.propietario=this.propietario;
            this.inspeccion.vehicle=this.vehiculo;
            this.$api.post('inspection',this.inspeccion).then(response=>{
                console.log(response.data);
                this.getInspecciones();
                this.dialogRegistro=false;
                this.getLineas();
                this.getMarcas();
                this.getModelos();
                this.getTipos();
            })
        },
        formulario(){
            this.propietario={'seguro':'SOAT'};
            this.vehiculo={};
            this.inspeccion={'calificacion':'NO CUMPLE'};
            this.dialogRegistro=true;
        },
        getTipos(){
            this.tipos=[]
            this.$api.get('listTipo').then(response=>{
                console.log(response.data); 
                response.data.forEach(r => {
                   this.tipos.push(r.nombre); 
                });
            })
        },
        getMarcas(){
            this.marcas=[]
            this.$api.get('listMarca').then(response=>{
                console.log(response.data);
                response.data.forEach(r => {
                    this.marcas.push(r.marca); 
                 });
            })
        },
        getModelos(){
            this.modelos=[] 
            this.$api.get('listModelo').then(response=>{
                console.log(response.data);
                response.data.forEach(r => {
                    this.modelos.push(r.modelo);
                });
            })
        },
        getLineas(){
            this.lineas=[];
            this.$api.get('listLinea').then(response=>{
                response.data.forEach(r => {
                    this.lineas.push(r.linea);
                });
            })
        },
        getInspecciones(){
            this.$api.get('listInsp/'+this.fecha).then(response=>{
                this.inspecciones=response.data;
            })
        }

    }
}
</script>