<div id="layoutSidenav_content">
    <main>






        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo ?></h4>
            <form id="form_permisos" name="form_permisos" method="POST" action="<?php echo base_url() . '/roles/guardaPermisos'; ?>">
                <input type="hidden" name="id_rol" value="<?php echo $id_rol; ?>">

                <?php foreach ($permisos as $permiso) { ?>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="<?php echo $permiso['id']; ?>" name="permisos[]" <?php if (isset($asignado[$permiso['id']])) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?> />
                        <label class="form-check-label"><?php echo $permiso['nombre'] ?></label> <br />
                    </div>
                <?php } ?>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </main>