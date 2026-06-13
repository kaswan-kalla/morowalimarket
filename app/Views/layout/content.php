<?= $this->extend('layout/body') ?>


<?= $this->section('content') ?>

<!----modal_start-- -->
<div class="container">

    <?php
    $content = isset($content) ? $content : false;
    // $file = "../app/Views/{$content}/modal.php";
    // if (file_exists($file)) {
    //     echo $this->include("{$content}/modal"); //modal for internal
    // }
    // echo $this->include("layout/modal"); //modal for global

    $file=is_file(APPPATH . "Views/{$content}/modal.php") ;
    if ($file) {
        echo $this->include("{$content}/modal"); //modal for internal
    }
    echo $this->include("layout/modal"); //modal for global
    ?>
</div>
<!----modal_end-- -->


<?= $this->endSection() ?>