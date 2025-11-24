<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Visitor Logbook</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/favicon.svg'); ?>">
    <!-- Bootswatch Lux theme (Bootstrap 5) -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.1.3/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link.logout-btn {
            color: #dc3545 !important;
            transition: color 0.2s ease;
        }
        .nav-link.logout-btn:hover {
            color: #b02a37 !important;
        }
    </style>
    <!-- jQuery (required by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">Visitor Logbook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php $controller = strtolower($this->router->class ?? ''); ?>
                <ul class="navbar-nav me-auto">
                    <?php if ($controller !== 'auth'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('locations'); ?>">Locations</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link logout-btn" href="<?php echo site_url('auth/logout'); ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <?php if ($controller !== 'auth'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('auth/login'); ?>">Login</a>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container"><?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
    <?php endif; ?>