<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["email"]==='admin@admin.com') {
} else {
    header("location: login.php");
    exit;
}

$ruta= $_POST['ruta_gpx'];
$rutagpx = str_replace('\\', '/', $ruta);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Mapa Leaflet</title>
	 <script src="jquery-3.6.3.min.js"></script>
	 <link rel="stylesheet" href="leaflet/leaflet.css" />
	 <script src="leaflet/leaflet.js" ></script>
	 <script src="leaflet/gpx/gpx.js"></script>
	<style type="text/css">
		#mapa { 
				height: 500px; 
				width: 500px}
		#datos { 
        		height: 500px; 
        		width: 500px}
 	</style>
 	<script type="text/javascript">
 	
 	function msToTime(milliseconds) {
 	    //Get hours from milliseconds
 	    var hours = milliseconds / (1000*60*60);
 	    var absoluteHours = Math.floor(hours);
 	    var h = absoluteHours > 9 ? absoluteHours : '0' + absoluteHours;

 	    //Get remainder from hours and convert to minutes
 	    var minutes = (hours - absoluteHours) * 60;
 	    var absoluteMinutes = Math.floor(minutes);
 	    var m = absoluteMinutes > 9 ? absoluteMinutes : '0' +  absoluteMinutes;

 	    //Get remainder from minutes and convert to seconds
 	    var seconds = (minutes - absoluteMinutes) * 60;
 	    var absoluteSeconds = Math.floor(seconds);
 	    var s = absoluteSeconds > 9 ? absoluteSeconds : '0' + absoluteSeconds;

 	    return h == "00" ? m + ':' + s : h + ':' + m + ':' + s;
 	}
    $( document ).ready(function() {
            var map = L.map('mapa').setView([35.896, -5.29], 15);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: 'Programaci&oacute;n Web'
            }).addTo(map);
            //$('.leaflet-control-attribution').hide();
            $('.leaflet-control-attribution').html('Programaci&oacute;n Web');

            //Carga de una ruta GPX
            var gpxData = '<?php echo $rutagpx; ?>'; // URL to your GPX file or the GPX itself
            var gpx = new L.GPX(gpxData, {
                    async: true,
                    marker_options: {
                        startIconUrl: 'leaflet/gpx/images/pin-icon-start.png',
                        endIconUrl: 'leaflet/gpx/images/pin-icon-end.png',
                        shadowUrl: 'leaflet/gpx/images/pin-shadow.png'
                    },
                    polyline_options: {
                        color: 'red',
                        opacity: 0.75,
                        weight: 3,
                        lineCap: 'round'
                    }

                }).on('loaded', function(e) {
                    map.fitBounds(e.target.getBounds());
                    const inicio = new Date(gpx.get_start_time()).toLocaleString();
                    const fin = new Date(gpx.get_end_time()).toLocaleString();
                    const kms = (gpx.get_distance()/1000).toFixed(2);
                    const tiempoTotal = msToTime(gpx.get_total_time());
                    const tiempoMovimiento = msToTime(gpx.get_moving_time());
                }).addTo(map);	
            
            
    });	
 	</script>
 	
</head>
<body>

 <div id="mapa"></div>
 <button onclick="window.location.href = 'administrador.php';">Volver a inicio</button>
 <div id="datos"></div>

</body>
</html>