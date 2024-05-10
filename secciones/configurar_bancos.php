<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  
  $editar_banco = 'editar_banco.php?txtID=';       
}

$sentencia=$conexion->prepare("SELECT * FROM dtpmp");
$sentencia->execute();
$lista_bancos=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<br>
<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title textTabla">LISTA DE BANCOS </h2>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="lista_bancos" class="table table-bordered table-striped" style="text-align:center">
      <thead>
        <tr>
          <th>#</th>
          <th>Bancos</th>
          <th>Saldo</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
<?php $count = 0; ?>
<?php foreach ($lista_bancos as $registro) { ?>
    <tr>
        <td scope="row"><?php $count++; echo $count; ?></td>
        <td>Efectivo:</td>
        <td><?php echo '$' . number_format($registro['efectivo'], 0, '.', ','); ?></td>
        <td class="text-center">
            <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_banco;?><?php echo $registro['id']; ?>&banco=Efectivo" role="button" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
    <tr>
        <td scope="row"><?php $count++; echo $count; ?></td>
        <td>Davivienda:</td>
        <td><?php echo '$' . number_format($registro['davivienda'], 0, '.', ','); ?></td>
        <td class="text-center">
            <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_banco;?><?php echo $registro['id']; ?>&banco=Davivienda" role="button" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
    <tr>
        <td scope="row"><?php $count++; echo $count; ?></td>
        <td>Bancolombia:</td>
        <td><?php echo '$' . number_format($registro['bancolombia'], 0, '.', ','); ?></td>
        <td class="text-center">
            <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_banco;?><?php echo $registro['id']; ?>&banco=Bancolombia" role="button" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
    <tr>
        <td scope="row"><?php $count++; echo $count; ?></td>
        <td>Nequi:</td>
        <td><?php echo '$' . number_format($registro['nequi'], 0, '.', ','); ?></td>
        <td class="text-center">
            <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_banco;?><?php echo $registro['id']; ?>&banco=Nequi" role="button" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
<?php } ?>
</tbody>

    </table>
  </div>
</div>
<?php include("../templates/footer.php") ?>
