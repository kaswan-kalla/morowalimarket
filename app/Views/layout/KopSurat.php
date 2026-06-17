<?php if (isPMS()): ?>
    <div class="row m-4" id="kop-pms">
        <div class="col col-2 mb-2 border p-2">
            <img width="100%" src="<?= asset_url('asset/img/logo.jpg') ?>">

        </div>
        <div class="col col-6 mb-2 text-center border d-flex align-items-center justify-content-center text-center">
            <div class="w-100">

                <h4 class="row1">PT. PUTRA MOROWALI SEJAHTERA</h4>
                <div class="row2">Jl. Trans Sulawesi No.8 Keurea Bahodopi Morowali</div>
                <div class="row3">Email: putramorowali@gmail.com, Cp: 0822-2565-5079</div>
            </div>
        </div>
        <div class="col col-4 mb-2 border d-flex align-items-center justify-content-center text-center">
            <h1 id="dokumen-title"><?= $reportTitle ?? '#dokumen-title' ?></h1>
        </div>

    </div>
<?php endif; ?>
<?php if (isPFP()): ?>
    <div class="row m-4" id="kop-pfp">
        <div class="col col-2 mb-2 p-2 border p-1">
            <img width="80%" src="<?= asset_url('asset/img/logo_pfp.jpg') ?>">

        </div>
        <div class="col col-7 mb-2 text-center border d-flex align-items-center justify-content-center text-center">
            <div class="w-100">

                <h2 class="row1">PT. PUTRA FAISAL PERKASA</h2>
                <div class="row2">Jl. Trans Sulawesi No.8 Keurea Bahodopi Morowali</div>
                <div class="row3">Email: ptputrafaisalperkasa@gmail.com, Cp: 0813-4296-5154</div>
            </div>
        </div>
        <div class="col col-3 mb-2 border d-flex align-items-center justify-content-center text-center">
            <h2 id="dokumen-title"><?= $reportTitle ?? '#dokumen-title' ?></h2>
        </div>

    </div>
<?php endif; ?>

<div hidden>
    <div id='table-title-global'>
        <nav class="nav">
            <a id="btn-print--" class="nav-link p-0 ml-3 text-primary " href="javascript:void(0)">
                <i class="fa fa-print"></i> PRINT LAPORAN
            </a>
        </nav>
    </div>
</div>