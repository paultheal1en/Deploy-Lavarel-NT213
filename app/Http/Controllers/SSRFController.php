<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SSRFController extends Controller
{
    // Ham kiem tra URL co hop le hay khong
    private function isValidUrl($url)
    {
        // Xac thuc dinh dang URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Dam bao luoc do HTTPS
        if (parse_url($url, PHP_URL_SCHEME) !== 'https') {
            return false;
        }

        // Kiem tra cong chi cho phep 443
        $port = parse_url($url, PHP_URL_PORT);
        if ($port && $port !== 443) {
            return false;
        }

        // Kiem tra ip sau phan giai va chan dai ip noi bo
        $host = parse_url($url, PHP_URL_HOST);
        $ips = gethostbynamel($host);
        if ($ips === false || empty($ips)) {
            return false;
        }

        foreach ($ips as $ip) {
            if ($this->isPrivateIp($ip)) {
                return false;
            }
        }

        return true;
    }

    // Ham kiem tra ip co phai la dia chi noi bo khong
    private function isPrivateIp($ip)
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE
        ) === false;
    }
    
    public function index(Request $request)
    {
        $response = "";
        $url = $request->input('url');
    
        if ($url) {
            if ($this->isValidUrl($url)) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, 5); // Gioi han thoi gian cho
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); // Tat chuyen huong
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // Bat buoc xac thuc SSL
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // Kiem tra SSL chinh xac

                $response = curl_exec($curl);
    
                if (curl_errno($curl)) {
                    $response = 'Error: ' . curl_error($curl);
                } else if (strlen($response) > 10000) { // Gioi han kich thuoc phan hoi
                    $response = 'Error: Response size too large.';
                }
    
                curl_close($curl);
            } else {
                $response = "Invalid URL or URL not allowed.";
            }
        }

        return view('ssrf')->with('response', $response);
    }
}