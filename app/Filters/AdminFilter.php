<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter untuk halaman admin - cek role admin
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman admin');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
