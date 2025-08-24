<div class="page-wrap">
    <div class="app-sidebar colored">
        <div class="sidebar-header">
            <a class="header-brand" href="index.html">
                <div class="logo-img">
                   <img src="<?= base_url('theme/src/img/logo.svg') ?>" class="header-brand-img" alt="lavalite"> 
                </div>
                <span class="text">MJN-PRC</span>
            </a>
            <button type="button" class="nav-toggle"><i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i></button>
            <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
        </div>
        
        <div class="sidebar-content">
            <div class="nav-container">
                <nav id="main-menu-navigation" class="navigation-main">
                    
                    <div class="nav-item active">
                        <a href="<?= base_url('/') ?>"><i class="ik ik-bar-chart-2"></i><span>Dashboard</span></a>
                    </div>
                    <div class="nav-lavel">Master Data</div>
                    <div class="nav-item">
                        <a href="<?= base_url('data-categories') ?>"><i class="ik ik-clipboard"></i><span>Categories</span></a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= base_url('data-product') ?>"><i class="ik ik-package"></i><span>Products</span></a>
                    </div>
                    <div class="nav-lavel">Transaksi</div>
                    <div class="nav-item">
                        <a href="<?= base_url('data-incomingitems') ?>"><i class="ik ik-upload"></i><span>Incoming Product</span></a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= base_url('data-outgoingitems') ?>"><i class="ik ik-download"></i><span>Outgoing Product</span></a>
                    </div>
                </nav>
            </div>
        </div>
    </div>  