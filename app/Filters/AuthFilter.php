<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter autentikasi - cek apakah user sudah login
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('user_id')) {
            // Simpan URL yang ingin diakses untuk redirect setelah login
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Redirect user yang sudah login ke home jika akses halaman login/register
        $session = session();
        $path = $request->getUri()->getPath();

        if ($session->get('user_id') && in_array($path, ['login', 'register'])) {
            return redirect()->to('/');
        }
    }
}
