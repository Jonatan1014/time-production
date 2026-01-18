<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from coderthemes.com/hyper/layouts/pages-maintenance.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 15 Jul 2025 15:13:39 GMT -->
<head>
    <meta charset="utf-8" />
    <title>Sistema en Mantenimiento | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de gestión de tiempos y producción - Talleres Unidos Ltda" name="description" />
    <meta content="Talleres Unidos Ltda" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="api/assets/images/favicon.ico">
    <!-- Theme Config Js -->
    <script src="api/assets/js/hyper-config.js"></script>

    <!-- Vendor css -->
    <link href="api/assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="api/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="api/assets/css/unicons/css/unicons.css" rel="stylesheet" type="text/css" />
    <link href="api/assets/css/remixicon/remixicon.css" rel="stylesheet" type="text/css" />
    <link href="api/assets/css/mdi/css/materialdesignicons.min.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">

                    <div class="text-center">
                        <img src="api/assets/images/svg/maintenance.svg" height="140" alt="Sistema en mantenimiento">
                        <h3 class="mt-4">🔧 ¡Ups! El Sistema está en Mantenimiento</h3>
                        <p class="text-muted"><strong>Parece que alguien de sistemas "mejoró" algo...</strong> Estamos trabajando para arreglarlo (otra vez).</p>
                        <p class="text-muted">No te preocupes, volveremos pronto. Probablemente.</p>

                        <div class="row mt-5">
                            <div class="col-md-4">
                                <div class="text-center mt-3 ps-1 pe-1">
                                    <i class="ri-tools-line bg-primary maintenance-icon text-white mb-4"></i>
                                    <h5 class="text-uppercase">¿Por qué no funciona?</h5>
                                    <p class="text-muted">Nuestro querido "el de sistemas" decidió que era buena idea actualizar la base de datos un <?php 
                                        $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                                        echo $dias[date('w')];
                                    ?>. Ahora estamos pagando las consecuencias de su "mejora".</p>
                                </div>
                            </div> <!-- end col-->
                            <div class="col-md-4">
                                <div class="text-center mt-3 ps-1 pe-1">
                                    <i class="ri-time-line bg-primary maintenance-icon text-white mb-4"></i>
                                    <h5 class="text-uppercase">¿Cuánto Falta?</h5>
                                    <p class="text-muted">Según el departamento de sistemas: "5 minutos" (hace 2 horas). En realidad, estamos aplicando reparaciones y mejoras urgentes. Te prometemos que no tardaremos... mucho.</p>
                                </div>
                            </div> <!-- end col-->
                            <div class="col-md-4">
                                <div class="text-center mt-3 ps-1 pe-1">
                                    <i class="ri-customer-service-2-line bg-primary maintenance-icon text-white mb-4"></i>
                                    <h5 class="text-uppercase">¿Necesitas Ayuda?</h5>
                                    <p class="text-muted">Por favor, NO llames al de sistemas. Ya están lo suficientemente ocupados tratando de arreglar su propio desastre. Gracias por tu paciencia mientras solucionamos este "pequeño" inconveniente. ☕</p>
                                </div>
                            </div> <!-- end col-->
                        </div> <!-- end row-->
                    </div> <!-- end /.text-center-->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        <script>document.write(new Date().getFullYear())</script> © Time Production - Talleres Unidos Ltda
    </footer>
    <!-- Vendor js -->
    <script src="api/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="api/assets/js/app.js"></script>

    <!-- Script de verificación automática de conexión -->
    <script>
        // Verificar conexión cada 20 segundos
        setInterval(function() {
            fetch('verificar-conexion.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Conexión restaurada - redirigir al index
                        console.log('Conexión restaurada, redirigiendo...');
                        window.location.href = 'index.php';
                    } else {
                        console.log('Aún sin conexión a la base de datos');
                    }
                })
                .catch(error => {
                    console.log('Error al verificar conexión:', error);
                });
        }, 20000); // 20 segundos

        // Verificación inicial después de 5 segundos
        setTimeout(function() {
            fetch('verificar-conexion.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.log('Error en verificación inicial:', error);
                });
        }, 5000);
    </script>

</body>


<!-- Mirrored from coderthemes.com/hyper/layouts/pages-maintenance.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 15 Jul 2025 15:13:39 GMT -->
</html>