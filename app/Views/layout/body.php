<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $judul ?? ''; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('asset/pavicon.ico') ?>" type="image/x-icon">

    <!-- CSS -->
    <link href="<?= base_url('asset/css/bootstrap.css?v=' . filemtime(FCPATH . 'asset/css/bootstrap.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/bootstrap-datepicker3.min.css?v=' . filemtime(FCPATH . 'asset/css/bootstrap-datepicker3.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/slidebars.css?v=' . filemtime(FCPATH . 'asset/css/slidebars.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/sweetalert2.min.css?v=' . filemtime(FCPATH . 'asset/css/sweetalert2.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/jtable/themes/lightcolor/gray/jtable.min.css?v=' . filemtime(FCPATH . 'asset/jtable/themes/lightcolor/gray/jtable.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/jquery-ui.min.css?v=' . filemtime(FCPATH . 'asset/css/jquery-ui.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/bootstrapSelect.css?v=' . filemtime(FCPATH . 'asset/css/bootstrapSelect.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/my_style.css?v=' . filemtime(FCPATH . 'asset/css/my_style.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/bootstrap-editable.css?v=' . filemtime(FCPATH . 'asset/css/bootstrap-editable.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/jquery-flexdatalist-2.3.0/jquery.flexdatalist.min.css?v=' . filemtime(FCPATH . 'asset/jquery-flexdatalist-2.3.0/jquery.flexdatalist.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/select2.min.css?v=' . filemtime(FCPATH . 'asset/css/select2.min.css')); ?>" rel="stylesheet">
    <link href="<?= base_url('asset/css/photoviewer.min.css?v=' . filemtime(FCPATH . 'asset/css/photoviewer.min.css')); ?>" rel="stylesheet">
    <script>
        function base_url(str = '') {
            return "<?= base_url() ?>/" + str;
        }
    </script>
    <script src="<?= base_url('asset/js/my_function.js'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true
        });
    </script>
</head>

<body>

    <?php
    $content = $content ?? false;
    if ($content === 'Login') {
        echo $this->include('Login/index');
    } else {
        echo $this->include('layout/sidebar');
    }

    echo $this->renderSection('content');
    ?>

    <!-- JS -->
    <script src="<?= base_url('asset/js/jquery-3.3.1.js'); ?>"></script>
    <script src="<?= base_url('asset/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/jquery-form.js'); ?>"></script>
    <script src="<?= base_url('asset/js/solid.js'); ?>"></script>
    <script src="<?= base_url('asset/js/sweetalert2.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/date-time-picker.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/fontawesome.min.js'); ?>"></script>
    <script src="<?= base_url('asset/jtable/jquery.jtable.js'); ?>"></script>
    <script src="<?= base_url('asset/js/popper.js'); ?>"></script>
    <script src="<?= base_url('asset/js/bootstrap.js'); ?>"></script>
    <script src="<?= base_url('asset/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/velocity.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/velocity.ui.js'); ?>"></script>
    <script src="<?= base_url('asset/js/jquery.floatThead.js'); ?>"></script>
    <script src="<?= base_url('asset/js/printThis.js'); ?>"></script>
    <script src="<?= base_url('asset/js/bootstrapSelect.js'); ?>"></script>
    <script src="<?= base_url('asset/js/bootstrap-editable.js'); ?>"></script>
    <script src="<?= base_url('asset/js/easy.qrcode.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/bower_components/mark.js/dists/jquery.mark.es6.js'); ?>"></script>
    <script src="<?= base_url('asset/jquery-flexdatalist-2.3.0/jquery.flexdatalist.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/moment-with-locales.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/easy-number-separator.js'); ?>"></script>
    <script src="<?= base_url('asset/js/jquery.caret.js'); ?>"></script>
    <script src="<?= base_url('asset/js/clipboard.min.js'); ?>"></script>
    <script src="<?= base_url('asset/jqBarGraph/jqBarGraph.1.1.js'); ?>"></script>
    <script src="<?= base_url('asset/js/html2canvas.js'); ?>"></script>
    <script src="<?= base_url('asset/js/select2.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/photoviewer.min.js'); ?>"></script>
    <script src="<?= base_url('asset/js/terbilang.js'); ?>"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="<?= base_url('asset/js/ChartHelper.js'); ?>"></script>
    <?= $this->include('js_helper') ?>

    <?php
    // JS Dinamis berdasarkan $content dan $script
    if (isset($script)) {
        foreach ($script as $s) {
            $path = "asset/js/view/{$content}/{$s}.js";
            echo '<script src="' . base_url($path . '?v=' . filemtime(FCPATH . $path)) . '"></script>';
        }
    }

    // Main view script
    $mainViewPath = "asset/js/view/{$content}.js";
    echo '<script src="' . base_url($mainViewPath . '?v=' . filemtime(FCPATH . $mainViewPath)) . '"></script>';


    echo '<script src="' . base_url('asset/js/my_script.js?v=' . filemtime(FCPATH . 'asset/js/my_script.js')) . '"></script>';
    echo '<script src="' . base_url('asset/js/my_test_input.js?v=' . filemtime(FCPATH . 'asset/js/my_test_input.js')) . '"></script>';

    // Modal scripts
    if (isset($modal)) {
        foreach ($modal as $m) {
            $path = "asset/js/modal/{$m}.js";
            echo '<script src="' . base_url($path . '?v=' . filemtime(FCPATH . $path)) . '"></script>';
        }
    }
    ?>

</body>

</html>