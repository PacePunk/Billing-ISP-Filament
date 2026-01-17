<?php

namespace App\Services;

$path = base_path('vendor/evilfreelancer/routeros-api-php/src');
if (!is_dir($path)) {
    die("GAWAT! Folder library tidak terbaca di: " . $path);
}
require_once $path . '/Interfaces/ClientInterface.php';
require_once $path . '/Client.php';

use EvilFreelancer\RouterOS\Client;
use Exception;

class MikrotikService
{
    public static function createSecret($router, $user, $pass, $profile)
    {

        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password, 
                'port' => (int) $router->port,
                'timeout' => 3, 
            ]);
        } catch (Exception $e) {
            throw new Exception("Gagal Konek ke Mikrotik: " . $e->getMessage());
        }

        $cek = $client->query('/ppp/secret/print')
            ->where('name', $user)
            ->read();

        if (!empty($cek)) {
            throw new Exception("Username PPPoE '$user' sudah ada di Mikrotik!");
        }

        $client->query('/ppp/secret/add')
            ->equal('name', $user)
            ->equal('password', $pass)
            ->equal('profile', $profile)
            ->equal('service', 'pppoe') 
            ->write();
            
        return true;
    }
}