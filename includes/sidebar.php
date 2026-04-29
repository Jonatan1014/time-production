<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">
    <!-- Brand Logo Light -->
    <a href="index.php" class="logo logo-light mb-4">
        <span class="logo-lg text-white my-3">
            <h3>Time Production</h3>
        </span>
        <span class="logo-sm text-white ">
            <h3>TP</h3>
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.php" class="logo logo-dark ">
        <span class="logo-lg">
            <img src="assets/images/logo-dark.png" alt="dark logo" />
        </span>
        <span class="logo-sm">
            <img src="assets/images/logo-dark-sm.png" alt="small logo" />
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->


        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title">Menú Principal</li>

            <li class="side-nav-item">
                <a href="index.php" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title">Gestión de Horas</li>

            <li class="side-nav-item">
                <a href="mis-horas.php" class="side-nav-link">
                    <i class="ri-time-line"></i>
                    <span> Mis Horas </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="registrar-horas.php" class="side-nav-link">
                    <i class="ri-add-circle-line"></i>
                    <span> Registrar Horas </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="horas-extras.php" class="side-nav-link">
                    <i class="ri-timer-flash-line"></i>
                    <span> Horas Extras </span>
                    <?php 
                    $rol_usuario = strtolower($_SESSION['user_rol'] ?? '');
                    $es_admin = ($rol_usuario === 'administrador' || $rol_usuario === 'root');
                    $es_dotacion = ($rol_usuario === 'administrador_dotacion' || $es_admin);
                    
                    if ($es_admin):
                        // Mostrar badge con solicitudes pendientes
                        require_once 'includes/Class-horas-extras.php';
                        $HoraExtra_class = new HoraExtra();
                        $estadisticas_he = $HoraExtra_class->obtenerEstadisticas();
                        if ($estadisticas_he['pendientes'] > 0):
                    ?>
                    <span
                        class="badge bg-danger rounded-pill float-end"><?php echo $estadisticas_he['pendientes']; ?></span>
                    <?php endif;
                    endif; ?>
                </a>
            </li>

            <?php if ($es_dotacion): ?>
            <li class="side-nav-title">Dotacion</li>

            <li class="side-nav-item">
                <a href="dotaciones.php" class="side-nav-link">
                    <i class="mdi mdi-clipboard-list-outline"></i>
                    <span> Entregas de Dotacion </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="dotaciones-items.php" class="side-nav-link">
                    <i class="mdi mdi-package-variant"></i>
                    <span> Items de Dotacion </span>
                </a>
            </li>
            <?php endif; ?>



            <?php 
            $es_produccion = ($rol_usuario === 'produccion' || $es_admin);
            if ($es_produccion): ?>
            <!-- <li class="side-nav-item">
                <a href="registrar-horas-produccion.php" class="side-nav-link">
                    <i class="ri-edit-line"></i>
                    <span> Registro de Horas </span>
                </a>
            </li> -->
            <?php endif; ?>

            <?php if ($es_admin): ?>
            <li class="side-nav-title">Órdenes de Producción</li>

            <li class="side-nav-item">
                <a href="ordenes-produccion.php" class="side-nav-link">
                    <i class="ri-file-list-3-line"></i>
                    <span> Órdenes de Producción </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="add-orden-produccion.php" class="side-nav-link">
                    <i class="ri-file-add-line"></i>
                    <span> Nueva Orden </span>
                </a>
            </li>

            <li class="side-nav-title">Reportes</li>

            <li class="side-nav-item">
                <a href="reportes.php" class="side-nav-link">
                    <i class="ri-bar-chart-box-line"></i>
                    <span> Reportes de Productividad </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="reporte-produccion.php" class="side-nav-link">
                    <i class="ri-file-chart-line"></i>
                    <span> Reporte de Producción </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="reporte-costos-usuarios.php" class="side-nav-link">
                    <i class="mdi mdi-currency-usd"></i>
                    <span> Costos por Usuario </span>
                </a>
            </li>

            <li class="side-nav-title">Integraciones</li>

            <li class="side-nav-item">
                <a href="sincronizar-projectdashboard.php" class="side-nav-link">
                    <i class="mdi mdi-cloud-sync"></i>
                    <span> Sincronizar Horas </span>
                    <?php 
                        // Mostrar badge con registros pendientes
                        require_once 'includes/Class-sincronizacion.php';
                        $Sinc_class = new Sincronizacion();
                        $stats_sinc = $Sinc_class->obtenerEstadisticas();
                        if ($stats_sinc['pendientes_total'] > 0):
                        ?>
                    <span
                        class="badge bg-warning rounded-pill float-end"><?php echo $stats_sinc['pendientes_total']; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="side-nav-title">Administración</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarUsuarios" aria-expanded="false"
                    aria-controls="sidebarUsuarios" class="side-nav-link">
                    <i class="ri-group-line"></i>
                    <span> Gestión de Usuarios </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarUsuarios">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="usuarios.php">
                                <i class="ri-user-line"></i> Listado de Usuarios
                            </a>
                        </li>
                        <li>
                            <a href="add-usuarios.php">
                                <i class="ri-user-add-line"></i> Crear Usuario
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="departamentos.php" class="side-nav-link">
                    <i class="ri-building-line"></i>
                    <span> Departamentos </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="cargos.php" class="side-nav-link">
                    <i class="mdi mdi-briefcase"></i>
                    <span> Cargos </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="configuracion.php" class="side-nav-link">
                    <i class="ri-settings-3-line"></i>
                    <span> Configuración </span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <!--- End Sidemenu -->

        <!-- Espacio adicional al final del sidebar para mejor visualización -->
        <div class="sidebar-footer-spacer" style="height: 60px;"></div>

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->