<form>
    <?php if ($_SESSION['rol'] == 1) : ?>
        <button type="submit" name="orden" value="Nuevo">Cliente Nuevo</button><br>
    <?php endif; ?>
</form>
<br>

<table>
<tr>
    <?php
    // Array de nombres de columna
    $columnas = ['id', 'first_name', 'email', 'gender', 'ip_address', 'telefono'];

    // Iterar sobre cada columna y generar el encabezado de la tabla
    foreach ($columnas as $columna) : ?>
        <th><a href="?ordenacion=<?= $columna ?>"><?= $columna ?></a></th>
    <?php endforeach ?>
</tr>

<?php foreach ($tvalores as $valor) : ?>
    <tr>
        <td><?= $valor->id ?></td>
        <td><?= $valor->first_name ?></td>
        <td><?= $valor->email ?></td>
        <td><?= $valor->gender ?></td>
        <td><?= $valor->ip_address ?></td>
        <td><?= $valor->telefono ?></td>
        <?php if ($_SESSION['rol'] == 1) : ?>
            <td><a href="#" onclick="confirmarBorrar('<?= $valor->first_name ?>',<?= $valor->id ?>);">Borrar</a></td>
            <td><a href="?orden=Modificar&id=<?= $valor->id ?>">Modificar</a></td>
        <?php endif; ?>
        <td><a href="?orden=Detalles&id=<?= $valor->id ?>">Detalles</a></td>
    </tr>
<?php endforeach ?>
</table>

<form>
    <button type="submit" name="nav" value="Primero"><<</button>
    <button type="submit" name="nav" value="Anterior"><</button>
    <button type="submit" name="nav" value="Siguiente">></button>
    <button type="submit" name="nav" value="Ultimo">>></button>
</form>
