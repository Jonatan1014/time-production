<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">
            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="assets/images/logo.png" alt="logo" />
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="small logo" />
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="dark logo" />
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo-dark-sm.png" alt="small logo" />
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line font-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search ..."
                            aria-label="Recipient's username" />
                    </form>
                </div>
            </li>

          

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="Tema">
                    <i class="ri-moon-line font-22"></i>
                </div>
            </li>

            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="#" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line font-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <?php
                        // Determinar imagen de usuario segura y con fallback
                        $default_img = 'assets/images/uploads/users/image.png';
                        $user_img_path = $default_img;

                        if (session_status() === PHP_SESSION_NONE) {
                            @session_start();
                        }

                        if (!empty(
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            isset($_SESSION['user_img'])
                        ) && !empty($_SESSION['user_img'])) {
                            // Posibles rutas relativas que podrían estar guardadas en la DB
                            $possible = [
                                __DIR__ . '/../' . ltrim($_SESSION['user_img'], '/'),
                                __DIR__ . '/../' . ltrim(str_replace('/user/', '/users/', $_SESSION['user_img']), '/'),
                                __DIR__ . '/../assets/images/users/' . basename($_SESSION['user_img'])
                            ];

                            foreach ($possible as $p) {
                                if (file_exists($p)) {
                                    // Convertir a ruta relativa desde el webroot
                                    $user_img_path = str_replace(realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR, '', realpath($p));
                                    $user_img_path = str_replace('\\', '/', $user_img_path);
                                    break;
                                }
                            }
                        }
                        ?>
                        <img src="<?php echo $user_img_path; ?>" alt="user-image" width="32" class="rounded-circle" />
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0"><?php echo $_SESSION['user_nombre']; ?></h5>
                        <h6 class="my-0 fw-normal"><?php echo ucfirst($_SESSION['user_rol']); ?></h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Bienvenido !</h6>
                    </div>

                    <!-- item-->
                    <a href="mi-perfil.php" class="dropdown-item">
                        <i class="mdi mdi-account-circle me-1"></i>
                        <span>Mi cuenta</span>
                    </a>

                    <!-- item-->
                    <!-- <a href="javascript:void(0);" class="dropdown-item">
                        <i class="ri-settings-3-line me-1"></i>
                        <span>Configuración</span>
                    </a> -->
                    <!-- item-->
                    <a href="action/session-destroy.php" class="dropdown-item">
                        <i class="mdi mdi-logout me-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>