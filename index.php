<?php
$pageTitle = "Login";
include 'header.php';
?>
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-12 col-md-4 h-100">
            <div class="row justify-content-center h-100 align-items-center contenedorLogin">
                <div class="col-12 formLogin">
                    <form action="iniciar_sesion.php" method="POST" class="p-4">
                        <div class="d-flex justify-content-center"><img src="img/logoLogin.png" alt="logo la toxica">
                        </div>
                        <div class="col border-top border-white my-5"></div>
                        <!-- <h1 class="text-center mb-4 text-white">Iniciar Sesión</h1> -->
                        <div class="mb-3">
                            <label for="usuario" class="form-label text-white"><i
                                    class="fa-solid fa-user text-white"></i>
                                Usuario</label>
                            <input type="text" name="Usuario" class="form-control" id="usuario"
                                placeholder="Nombre de Usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label text-white"><i
                                    class="fa-solid fa-unlock text-white"></i>
                                Clave</label>
                            <input type="password" name="Clave" class="form-control" id="clave" placeholder="Clave"
                                required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary shadow mt-4">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-none d-md-block col-12 col-md-8 fondoLogin h-100">
        </div>
    </div>

</div>
<?php
include 'common_scripts.php';
?>
</body>

</html>