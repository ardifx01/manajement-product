<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (!session()->get('isLoggedIn')) {
            return service('response')->setJSON([
                'status' => 'error',
                'message' => 'Anda harus login terlebih dahulu'
            ])->setStatusCode(401);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}