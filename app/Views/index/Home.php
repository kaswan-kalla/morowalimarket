<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Start - Test</title>
    <link rel="stylesheet" href="<?= base_url('public/asset/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/asset/fontawesome/css/all.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .start-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .start-card h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .start-card p {
            color: #666;
            font-size: 1.1rem;
        }

        .status-badge {
            display: inline-block;
            background: #28a745;
            color: #fff;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-top: 20px;
        }

        .info-table {
            text-align: left;
            margin-top: 25px;
            width: 100%;
        }

        .info-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: #333;
            width: 40%;
        }
    </style>
</head>

<body>
    <div class="start-card">
        <div style="font-size: 4rem; color: #667eea; margin-bottom: 15px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Aplikasi Berjalan!</h1>
        <p>Halaman start untuk test berhasil dimuat.</p>
        <span class="status-badge"><i class="fas fa-power-off"></i> Sistem Aktif</span>

        <table class="info-table">
            <tr>
                <td>Framework</td>
                <td>CodeIgniter 4</td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <td>Environment</td>
                <td><?= ENVIRONMENT ?></td>
            </tr>
            <tr>
                <td>Base URL</td>
                <td><?= base_url() ?></td>
            </tr>
            <tr>
                <td>Waktu Server</td>
                <td><?= date('d-m-Y H:i:s') ?></td>
            </tr>
        </table>
    </div>
</body>

</html>