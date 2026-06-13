<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Maintenance') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 50px 30px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        h1 {
            color: #2d3748;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .message {
            color: #718096;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .info-item {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item .icon-small {
            font-size: 20px;
            margin-right: 10px;
            margin-top: 2px;
        }

        .info-item strong {
            color: #2d3748;
            font-weight: 600;
        }

        .info-item span {
            color: #4a5568;
        }

        .progress-bar {
            background: #e2e8f0;
            border-radius: 10px;
            height: 10px;
            margin: 30px 0;
            overflow: hidden;
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 50%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            animation: progress 2s ease-in-out infinite;
        }

        @keyframes progress {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(200%);
            }
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #a0aec0;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #cbd5e0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .message {
                font-size: 16px;
            }

            .icon {
                font-size: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">🔧</div>

        <h1>Sedang Dalam Perbaikan</h1>

        <p class="message"><?= esc($message ?? 'Maaf, sistem sedang dalam maintenance untuk peningkatan layanan.') ?></p>

        <div class="progress-bar"></div>

        <div class="info-box">
            <?php if (!empty($estimated_time)): ?>
                <div class="info-item">
                    <span class="icon-small">⏱️</span>
                    <div>
                        <strong>Estimasi Selesai:</strong><br>
                        <span><?= esc($estimated_time) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($contact)): ?>
                <div class="info-item">
                    <span class="icon-small">📧</span>
                    <div>
                        <strong>Hubungi Kami:</strong><br>
                        <span><?= esc($contact) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="info-item">
                <span class="icon-small">ℹ️</span>
                <div>
                    <strong>Informasi:</strong><br>
                    <span>Kami akan segera kembali. Terima kasih atas pengertian Anda.</span>
                </div>
            </div>
        </div>

        <div class="contact-info">
            Jika Anda memerlukan bantuan mendesak, silakan hubungi tim IT Support kami.
        </div>

        <div class="footer">
            &copy; <?= date('Y') ?> - Inventory Management System
        </div>
    </div>

    <script>
        // Auto refresh setiap 2 menit untuk cek apakah maintenance sudah selesai
        setTimeout(function() {
            window.location.reload();
        }, 120000); // 120000 ms = 2 menit
    </script>
</body>

</html>