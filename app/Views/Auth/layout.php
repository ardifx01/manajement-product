<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?= base_url('theme/favicon.ico') ?>">

    <title>Login</title>

    <link rel="icon" href="<?= base_url('theme/favicon.ico') ?>" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('theme/plugins/bootstrap/dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/ionicons/dist/css/ionicons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/icon-kit/dist/css/iconkit.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/perfect-scrollbar/css/perfect-scrollbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/dist/css/theme.min.css') ?>">
    <script src="<?= base_url('theme/src/js/vendor/modernizr-2.8.3.min.js') ?>"></script>

    <?= $this->renderSection('pageStyles') ?>
</head>

<body>

<main >
	<?= $this->renderSection('main') ?>
</main>


<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= base_url('theme/src/js/vendor/jquery-3.3.1.min.js') ?>"><\/script>')</script>
        <script src="<?= base_url('theme/plugins/popper.js/dist/umd/popper.min.js') ?>"></script>
        <script src="<?= base_url('theme/plugins/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
        <script src="<?= base_url('theme/plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js') ?>"></script>
        <script src="<?= base_url('theme/plugins/screenfull/dist/screenfull.js') ?>"></script>
        <script src="<?= base_url('theme/dist/js/theme.js') ?>"></script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
<?= $this->renderSection('pageScripts') ?>
</body>
</html>
