<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MaintenanceFilter implements FilterInterface
{
    /**
     * Check if maintenance mode is active
     * 
     * @param RequestInterface $request
     * @param null             $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah maintenance mode aktif
        $maintenanceMode = getenv('MAINTENANCE_MODE');

        if ($maintenanceMode === 'true') {
            // Dapatkan IP address user
            $userIP = $request->getIPAddress();

            // IP yang diizinkan akses saat maintenance (localhost untuk development)
            $allowedIPs = ['127.0.0.1', '::1', '2404:c0:6710::269:eda1'];

            // Jika IP tidak ada dalam daftar yang diizinkan, tampilkan halaman maintenance
            if (!in_array($userIP, $allowedIPs)) {
                // Set response code 503 Service Unavailable
                $response = service('response');
                $response->setStatusCode(503);

                // Set header Retry-After (estimasi 1 jam = 3600 detik)
                $response->setHeader('Retry-After', '3600');

                // Gunakan output buffering untuk include file standalone HTML
                // APPPATH menggunakan case yang benar untuk Linux compatibility
                $viewPath = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . 'Maintenance.php';

                // Pastikan file ada sebelum include (penting untuk Linux case-sensitive)
                if (!file_exists($viewPath)) {
                    log_message('error', '[MaintenanceFilter] View file not found: ' . $viewPath);
                    // Fallback: tampilkan error sederhana
                    return $response->setBody('<h1>Maintenance Mode</h1><p>Sistem sedang dalam maintenance.</p>');
                }

                // Extract variables untuk view
                $title = 'Sedang Dalam Perbaikan';
                $message = 'Maaf, sistem sedang dalam maintenance untuk peningkatan layanan.';
                $estimated_time = env('MAINTENANCE_ESTIMATED_TIME', 'Beberapa saat lagi');
                $contact = env('MAINTENANCE_CONTACT', 'Software Engineering Division');

                // Include dan capture output
                ob_start();
                include $viewPath;
                $html = ob_get_clean();

                return $response->setBody($html);
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * as needed. This method is a last resort and should be avoided.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param null              $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
