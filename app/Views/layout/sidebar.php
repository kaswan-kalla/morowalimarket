<?php session('username') || die() ?>

<?php
/**
 * @var string $judul
 * @var string $content
 */

$db = \Config\Database::connect();
$tanggal = date('Y-m-d');
$bln = (int)date('m', strtotime($tanggal));
$thn = (int)date('Y', strtotime($tanggal));
$thnStart = 2023;
$j = $thn - $thnStart;

$role = $db->table('t_role');
$role->where('role_id', session('role_id'));
$role->select('*');
$role = $role->get()->getRowArray();
// dd($role);
$gudang = $role['role'] == "gudang site" ? session('gudang_id') : '';
$posisi = $role['role'] . " " . $gudang;

$blocked = $db->table('t_user_menu_blocked');
$blocked->where('role_id', session('role_id'));
$blocked->select('*');
$blocked = $blocked->get()->getResultArray();
$blockedMenu = array_column($blocked, 'menu_id');

$blocked = $db->table('t_user_menu_sub_blocked');
$blocked->where('role_id', session('role_id'));
$blocked->select('*');
$blocked = $blocked->get()->getResultArray();
$blockedSub = array_column($blocked, 'menu_id');
$isShowId = false;
?>


<div class="wrapper">

    <!-- Sidebar Holder -->
    <nav id="sidebar">
        <div class="sidebar-header pt-1 p-0">
            <?php if (strpos(current_url(), 'test') !== false): ?>
                <div class="text-center mt-2">
                    <span class="badge badge-warning px-3 py-1 blink-gentle" style="font-size: 1rem; animation: blinkGentle 2s infinite;">
                        <i class="fas fa-flask mr-1"></i>Web Testing
                    </span>
                </div>
            <?php else: ?>
                <img class="d-block m-auto" src="<?= base_url('asset/img/logo-no-bg.png') ?>" width="100" alt="">
                <!-- <h6 class='text-center text-dark font-weight-bold m-0'>PMS INVENTORY</h6> -->
            <?php endif; ?>
        </div>
        <hr class='hr-1'>

        <ul class="list-unstyled components">

            <p class='user-name text-truncate pr-2'>
                <i class="fas fa-user-circle fa-fw text-warning ml-2" aria-hidden="true"></i>
                <?= session('username') ? session('username') : 'NO USER' ?>
            </p>
            <input id="user_id" type="text" value="<?= session('user_id') ?>" hidden>

            <div class='user-divisi pr-2'>
                <span class="text-truncate" id="divisi_login">
                    <?= $posisi  ?>
                </span>
            </div>
            <hr class='hr-1 mb-3 '>
            <div class="px-3 mb-2">
                <input type="search" id="sidebar-search" class="form-control form-control-sm rounded" placeholder="Cari menu (Ctrl+S)" aria-label="Cari menu" style="background-color: #eaeaea;" autocomplete="off" autofocus>
            </div>

            <?php
            $role_id = session('role_id');

            $queryMenu = "SELECT t_user_menu.id, menu, display 
                            FROM t_user_menu 
                            JOIN t_user_menu_access
                            ON  t_user_menu.id=t_user_menu_access.menu_id
                            WHERE t_user_menu_access.role_id= $role_id
                            ORDER BY t_user_menu.sorter ASC
                            ";
            $menu = $db->query($queryMenu)->getResultArray();
            ?>

            <?php foreach ($menu as $m) : ?>

                <div class="menu-header text-uppercase" id="menu-header<?= $m['id'] ?>"><?= $m['menu'] ?>
                </div>
                <div id="menu<?= $m['id'] ?>" class="mb-2" style="display:<?= $m['display'] ?>">
                    <!-- open Div submenu -->


                    <?php
                    $menu_id = $m['id'];
                    $queryMenu_sub = "SELECT * 
                                        FROM t_user_menu_sub 
                                        WHERE menu_id=$menu_id
                                        ORDER BY sorter ASC
                                        ";
                    $menu_sub = $db->query($queryMenu_sub)->getResultArray();
                    ?>

                    <?php foreach ($menu_sub as $ms) : ?>
                        <?php
                        // if ($ms['id'] == 1) break;
                        $pointer_active = " <i class='fas fa-caret-right text-warning pt-1'></i>";
                        if ($judul == $ms['title']) {
                            $menu_active = 'menu-active';
                            $eye = $pointer_active;
                        } else {
                            $menu_active = '';
                            $eye = '';
                        }

                        ?>

                        <?php if ($ms['url'] == "#dropdown-toggle") : ?>
                            <?php if (!in_array($ms['id'], $blockedMenu)) :  ?>
                                <li>
                                    <a href="#subtitle<?= $ms['id'] ?>" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-<?= $ms['icon'] ?> fa-fw" aria-hidden="true"></i>
                                        <?= $ms['title'] ?></a>
                                    <ul class="collapse list-unstyled" id="subtitle<?= $ms['id'] ?>">
                                        <?php
                                        $sub_menu_id = $ms['id'];
                                        $query_sub_title = "SELECT * 
                                                        FROM t_user_menu_sub_down 
                                                        WHERE sub_menu_id=$sub_menu_id
                                                        ORDER BY sorter  ASC";
                                        $menu_sub_title = $db->query($query_sub_title)->getResultArray();
                                        ?>
                                        <?php foreach ($menu_sub_title as $mst) : ?>
                                            <?php


                                            // $url=strpos($mst['url'],'/')?'/'.$mst['id_name']:'';

                                            if ($content == $mst['url']) {
                                                $menu_active = 'menu-active';
                                                $eye = $pointer_active;
                                            } else {
                                                $menu_active = '';
                                                $eye = '';
                                            }


                                            ?>
                                            <?php if (!in_array($mst['id'], $blockedSub)) :  ?>
                                                <li>
                                                    <a href="<?= base_url($mst['url']) ?>" class="<?= $menu_active ?>" id="<?= $mst['id_name'] ?>" name="<?= $mst['id_name'] ?>"><i class="fa fa-<?= $mst['icon'] ?> fa-fw" aria-hidden="true"></i><?= $isShowId ? $mst['id'] : '' ?>
                                                        <?= $mst['sub_title'] ?><?= $eye ?></a>
                                                </li>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </ul>

                                </li>
                            <?php endif ?>
                        <?php else :  ?>
                            <?php if (!in_array($ms['id'], $blockedMenu)) :  ?>
                                <li>
                                    <a href="<?= base_url($ms['url']) ?>" class="<?= $menu_active ?>" id="<?= $ms['id_name'] ?>" name="<?= $ms['id_name'] ?>"><i class="fa fa-<?= $ms['icon'] ?> fa-fw" aria-hidden="true"></i><?= $isShowId ? $ms['id'] : '' ?> <?= $ms['title'] ?><?= $eye ?></a>
                                </li>
                            <?php endif ?>
                        <?php endif ?>

                    <?php endforeach ?>
                </div> <!-- close Div submenu -->
            <?php endforeach; ?>

            <div class='text-center mt-3'>
                <a class="rounded-circle logout-button m-auto logout" href="<?= base_url('login/logout') ?>"><i class="fas fa-sign-out-alt"></i></a>
            </div>

        </ul>
    </nav>

    <!-- Page Content Holder -->
    <div id="content" class="position-relative">
        <nav class="navbar navbar-expand-lg sticky-top mb-2 p-1 top-bar">
            <div class="container-fluid ">
                <button type="button" id="sidebarCollapse" class="navbar-btn rounded-circle">
                    <span></span>
                    <span id="menu-icon"></span>
                    <span></span>
                </button>
                <div class="col-8">
                    <div class="ml-3 row" style="display: none;" id="frame-set">
                        <div class="col text-left">
                            <button class="btn btn-primary rounded-right" id="btn-set-val">Set Value
                            </button>
                        </div>
                    </div>
                    <?php if (session('aut_id') != 0) : ?>
                        <div class="ml-3 row" style="display: none;" id="frame-send">
                            <div class="col text-left">
                                <button class="btn btn-primary rounded-right btn-send ">
                                    <i class="fa fa-envelope"></i>
                                    Ajukan Otorisasi<i class="fa fa-paper-plane ml-1 blinkColor" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="row" id="spinner" style="display: none;">
                            <div class="col text-center">
                                <div class="d-flex align-items-center justify-content-center vh-100">
                                    <div class="jtable-busy-message">Proses...</div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="row align-items-center" id='frame-periode' style="display: none;">
                        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="tahun" style="display: none;">Tahun</label>
                                </div>
                                <select class="custom-select" id="tahun">
                                    <option value="">-- SEMUA TAHUN --</option>
                                    <?php for ($i = 0; $i <= $j; $i++) : ?>
                                        <option value="<?= $thnStart + $i ?>" <?= $thn == $thnStart + $i ? "selected" : "" ?>><?= $thnStart + $i ?></option>
                                    <?php endfor ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-all-tahun" title="Semua Tahun">
                                        All
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="bulan" style="display: none;">Bulan</label>
                                </div>
                                <select class="custom-select" id="bulan">
                                    <option value="">-- SEMUA PERIODE --</option>
                                    <option value="1" <?= $bln == 1 ? "selected" : "" ?>>JANUARY</option>
                                    <option value="2" <?= $bln == 2 ? "selected" : "" ?>>FEBRUARI</option>
                                    <option value="3" <?= $bln == 3 ? "selected" : "" ?>>MARET</option>
                                    <option value="4" <?= $bln == 4 ? "selected" : "" ?>>APRIL</option>
                                    <option value="5" <?= $bln == 5 ? "selected" : "" ?>>MEI</option>
                                    <option value="6" <?= $bln == 6 ? "selected" : "" ?>>JUNI</option>
                                    <option value="7" <?= $bln == 7 ? "selected" : "" ?>>JULI</option>
                                    <option value="8" <?= $bln == 8 ? "selected" : "" ?>>AGUSTUS</option>
                                    <option value="9" <?= $bln == 9 ? "selected" : "" ?>>SEPTEMBER</option>
                                    <option value="10" <?= $bln == 10 ? "selected" : "" ?>>OKTOBER</option>
                                    <option value="11" <?= $bln == 11 ? "selected" : "" ?>>NOVEMBER</option>
                                    <option value="12" <?= $bln == 12 ? "selected" : "" ?>>DESEMBER</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-all-bulan" title="Semua Periode">
                                        All
                                    </button>
                                    <button class="btn btn-outline-secondary" type="button" id="btn-refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if (isAccDirektur() || isWakilDirektur() || isAccManager() || isAdmin() || isRolePurchase()) : ?>

                            <div class="col-12 col-sm-6 col-md-2 mb-2 mb-md-0">
                                <select id="company" class="form-control text-uppercase">
                                    <option value="0">DATA PMS</option>
                                    <option value="pfp" class="text-uppercase">DATA PFP</option>
                                </select>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>


                <!-- <div class="row"> -->


                <!-- </div> -->

                <button class="btn btn-dark d-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-align-justify"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="nav navbar-nav ml-auto">


                        <li class="nav-item ">
                            <a class="nav-link text-primary" id="btn-getdata-global" href="#" style="display: none;"><i class="fa fa-arrow-circle-down fw"></i> Get Data</a>
                        </li>
                        <li class="nav-item ">
                            <!-- <input id="varGlobal" type="text"> -->
                            <a class="nav-link text-primary" id="btn-export-global" href="#" style="display: none;"><i class="fa fa-file-excel fw"></i> Export</a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link text-primary" id="btn-print-global" href="#" style="display: none;" title="Print" data-toggle="tooltip" data-placement="top"><i class="fa fa-print fw"></i></a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('Home') ?>" class="nav-link text-primary" id="btn-back-home" style="display: none;">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a target="_blank" class="nav-link text-primary" id="btn-go-report" style="display: none;">
                                <i class="fa fa-receipt"></i> Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a target="_blank" class="nav-link text-primary" id="btn-go-grafik" style="display: none;">
                                <i class="fa fa-chart-line"></i> Grafik
                            </a>
                        </li>
                        <!--  Administrator -->
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link text-primary" href="<?= base_url('UserLog') ?>" target="_blank"><i class="fa fa-user fw"></i> User Activity</a>
                            </li>
                        <?php endif ?>
                        <li class="nav-item logout ">
                            <a class="nav-link text-primary" href="<?= base_url('login/logout') ?>"><i class="fa fa-sign-out-alt fw"></i> Logout</a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
        <!-- <div id="title-page" class='col-12 text-center font-weight-bold title-page'><XX?= $judul == 'Home' ? 'STOK PENERIMAAN TANGKI' : $judul ?></div> -->
        <!----content_start-- -->
        <?= $this->include("{$content}/index") ?>
        <!----content_end-- -->

        <!----tag_close content_ID-- -->
    </div>

    <!----tag_close wrapper_class-- -->
</div>

<?php if (isAccGudang() || isAccPurchase() || isAccManager() || isAccDirektur() || isAccFinance()): ?>
    <script>
        (function() {
            var t = setInterval(function() {
                if (typeof $ !== 'undefined' && $('#Otorisasi').length) {
                    clearInterval(t);
                    $.getJSON(base_url('Otorisasi/getBadgeCount'), function(res) {
                        if (res.total && res.total > 0) {
                            $('#Otorisasi').css({
                                'position': 'relative',
                                'display': 'inline-block'
                            }).append(' <span class="badge badge-danger otorisasi-badge" style="position:absolute;top:5px;font-size:10px;">' + res.total + '</span>');
                            var digits = String(res.total).length;
                            var rightVal = digits === 1 ? -7 : (digits === 3 ? -16 : -11);
                            $('.otorisasi-badge').css('right', rightVal + 'px');
                        }
                    });
                }
            }, 200);
        })();
    </script>
<?php endif; ?>