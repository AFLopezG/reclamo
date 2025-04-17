<template>
    <q-page>
        <div id="map" style="height: 90vh"></div>
    </q-page>
</template>
<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css'

export default {
    name:'posicionPage',
    data() {
        return {
            socket :this.$socket,
            mapa : null,
            centro: { lat: -17.968851, lng:-67.112892 },
            lat: -17.968851,
            lng: -67.112892,
            userMarker: null,
            zoom: 14,
            maxzoom: 18,
            iconSize: [30, 40],
            listMark:[],
            usuarios:[]
        }
        
    },
    mounted(){
     console.log('Conectando a socket...');
      this.inicializarMapa()
      this.misUsuarios() // Cargar datos y agregarlos al mapa
      
    this.socket.on('connect', () => console.log('✅ Conectado al servidor Socket.io'));
      // Escuchar eventos en tiempo real con Socket.io
      console.log(this.socket)
      this.$socket.on('actualizarConjunto', (data) => {
        console.log('Recibiendo actualización vía socket:', data);
        this.agregarOActualizarUser(data);
      });
    },
    methods:{
        inicializarMapa() {
      const streetLayer = L.tileLayer('https://cartodb-basemaps-a.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
      });
      let satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '© Google'
      });

      this.map = L.map('map').setView(this.centro, this.zoom, 12, 19);
      streetLayer.addTo(this.map);

      L.control
        .layers(
          {
            Calles: streetLayer,
            Satélite: satelliteLayer,
          },
          {}
        )
        .addTo(this.map);

        // Inicializar el grupo de marcadores
      this.grupoMarcadores = L.layerGroup().addTo(this.map);
const actualizarControl = L.Control.extend({
  options: {
    position: 'topright' // Ubicación del control en el mapa
  },
  onAdd: () => {
    // Crear un contenedor para el botón
    const container = L.DomUtil.create('div', 'leaflet-bar');
    
    // Crear el botón (aquí usamos un elemento HTML)
    const btn = document.createElement('button');
    btn.innerText = 'Actualizar';
    btn.style.backgroundColor = 'darkorange';  // Color primario de Quasar
    btn.style.border = 'none';
    btn.style.padding = '5px 10px';
    btn.style.color = 'white';
    btn.style.cursor = 'pointer';

    // Estilos de letra
    btn.style.fontFamily = 'Arial, sans-serif';  // Cambiar tipo de fuente
    btn.style.fontStyle = 'italic';  // Texto en cursiva
    btn.style.fontWeight = 'bold';  // Texto en negrita
    btn.style.fontSize = '12px';  // Tamaño de fuente

    // Evitar que el clic se propague al mapa
    L.DomEvent.disableClickPropagation(container);

    // Asignar el evento click para llamar a la función
    btn.onclick = () => {
      // Asegúrate de que '
      // ' esté en el alcance
      this.misUsuarios();
    };

    container.appendChild(btn);
    return container;
  }
});

// Agregar el control al mapa (suponiendo que 'this.map' es tu instancia de Leaflet)
this.map.addControl(new actualizarControl());


    },

    misUsuarios(){        
        this.$api.get('listPosicion').then(res => {
            console.log(res.data)
          this.usuarios = res.data; 
          if (this.grupoMarcadores) {
        this.map.removeLayer(this.grupoMarcadores);
        console.log('Se removió el grupo de marcadores anterior');
    }
  
  // Crear un nuevo grupo de marcadores y añadirlo al mapa
  this.grupoMarcadores = L.layerGroup().addTo(this.map);// Guardar los  obtenidos
          this.actualizarMapa(); // Mostrar en el mapa
        }).catch(error => {
          console.error('Error cargando puntos:', error);
        });
      },
      agregarOActualizarUser(point) {
        // Validar que  no sea nulo y que las coordenadas sean definidas (se permiten 0)
        if (!point || point.lat == null || point.lng == null) return;

        const index = this.usuarios.findIndex(c => c.id === point.id);

        if (index !== -1) {
            // Si ya existe, se elimina si las coordenadas son 0
            if (point.lat === 0 || point.lng === 0) {
            this.usuarios.splice(index, 1);
            } else {
            // Actualizar la información del 
            this.usuarios[index] = point;
            }
        } else {
            // Si no existe, agregarlo solo si las coordenadas son diferentes de 0
            if (point.lat !== 0 && point.lng !== 0) {
            this.usuarios.push(point);
            }
        }
        if (this.grupoMarcadores) {
            this.map.removeLayer(this.grupoMarcadores);
            console.log('Se removió el grupo de marcadores anterior');
        }
        this.actualizarMapa(); // Refrescar la vista en el mapa
    },

    actualizarMapa() { 
    if (!this.map) return;

  if (this.grupoMarcadores) {
    this.map.removeLayer(this.grupoMarcadores);
    console.log('Se removió el grupo de marcadores anterior');
  }
  
  // Crear un nuevo grupo de marcadores y añadirlo al mapa
  this.grupoMarcadores = L.layerGroup().addTo(this.map);
    this.usuarios.forEach(point => {
      if (!point.lat || !point.lng) return; // Ignorar si no tiene coordenadas

      const icono = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [50, 50]
      });


        // Crear un nuevo marcador si no existe
        const marker = L.marker([point.lat, point.lng], { icon: icono })
          .bindTooltip(point.nombre, { permanent: true })

        this.grupoMarcadores.addLayer(marker);
    });
    this.map.invalidateSize();
  },

    }
}
</script>