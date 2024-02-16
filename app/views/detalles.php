<hr>
<button onclick="location.href='./'" > Volver </button>
<br><br>
<table>
 <tr>
    <td>id:</td> 
    <td><input type="number" name="id" value="<?=$cli->id ?>"  readonly > </td>
    <td rowspan="7">
        <img src="<?=$banderaUrl ?>" alt="Bandera" width="40" height="auto">
        <img src="<?=$imagenCli ?>" alt="Foto Cliente" width="40" height="auto">
        <h2>Mapa Leaflet</h2>
        <!-- Contenedor para el mapa -->
        <div id="map" style="height: 200px; width: 370px;"></div>

        <!-- Agrega el JavaScript de Leaflet -->
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    </td>
 </tr>
 <tr>
    <td>first_name:</td> 
    <td><input type="text" name="first_name" value="<?=$cli->first_name ?>" readonly > </td>
 </tr>
 <tr>
    <td>last_name:</td> 
    <td><input type="text" name="last_name" value="<?=$cli->last_name ?>" readonly ></td>
 </tr>
 <tr>
    <td>email:</td> 
    <td><input type="email" name="email" value="<?=$cli->email ?>"   readonly  ></td>
 </tr>
 <tr>
    <td>gender</td> 
    <td><input type="text" name="gender" value="<?=$cli->gender ?>" readonly ></td>
 </tr>
 <tr>
    <td>ip_address:</td> 
    <td><input type="text" name="ip_address" value="<?=$cli->ip_address ?>" readonly ></td>
 </tr>
 <tr>
    <td>telefono:</td> 
    <td><input type="tel" name="telefono" value="<?=$cli->telefono ?>" readonly ></td>
 </tr>
</table>
 
<form>
<input type="hidden"  name="id" value="<?=$cli->id ?>">
<button type="submit" name="nav-detalles" value="Anterior"> Anterior << </button>
<button type="submit" name="nav-detalles" value="Siguiente"> Siguiente >> </button>
<button type="submit" name="nav-detalles" value="Imprimir"> Imprimir PDF </button>
</form>

<!--Script usando la api de "Leaflet" para sacar el mapa interactivo-->
<script>

function obtenerLatitudLongitud(ip) {
    return fetch("http://ip-api.com/json/" + ip)
        .then(response => response.json())
        .then(data => {
            if (data && data.status === 'success') {
                return { latitud: data.lat, longitud: data.lon, zoom: 13 };
            } else {
               return { latitud: -75.250973, longitud: -0.071389, zoom: 1 };
            }
        });
}

// Llamas a obtenerLatitudLongitud y usas then para manejar la respuesta
obtenerLatitudLongitud("<?=$cli->ip_address?>")
.then(coordenadas => {
        // Aquí puedes usar las coordenadas devueltas
        var map = L.map('map').setView([coordenadas.latitud, coordenadas.longitud], coordenadas.zoom);
        
        // Resto del código del mapa...
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
         }).addTo(map);

         L.marker([coordenadas.latitud, coordenadas.longitud]).addTo(map)
            .bindPopup('A pretty CSS popup.<br> Easily customizable.')
            .openPopup();
            })
    .catch(error => {
        console.error(error.message);
    });


</script>