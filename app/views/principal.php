<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD CLIENTES</title>
<link href="web/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="web/js/funciones.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body>
<div id="container" style="width: 950px;">
<div id="header">
<h1>MIS CLIENTES CRUD versión 1.1</h1>
</div>
<div id="aviso">
<?= $msg ?>
</div>
<div id="content">
<?= $contenido ?>
</div>
</div>
</body>
</html>