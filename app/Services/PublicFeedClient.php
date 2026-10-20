<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PublicFeedClient
{
    public function get(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (parse_url($url, PHP_URL_SCHEME) !== 'https' || ! $host || parse_url($url, PHP_URL_USER) || parse_url($url, PHP_URL_PASS) || (parse_url($url, PHP_URL_PORT) && parse_url($url, PHP_URL_PORT) !== 443)) {
            throw new RuntimeException('Use a public HTTPS feed on port 443.');
        }
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : gethostbynamel($host);
        if (! $ips) {
            throw new RuntimeException('Feed hostname could not be resolved.');
        }
        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new RuntimeException('Private and reserved network addresses are not allowed.');
            }
        }
        $response = Http::timeout(25)->connectTimeout(10)->withHeaders(['Accept' => 'application/rss+xml, application/atom+xml, application/xml', 'User-Agent' => 'AutoNews/1.0 RSS Reader'])
            ->withOptions(['allow_redirects' => false, 'curl' => [CURLOPT_RESOLVE => [$host.':443:'.$ips[0]]], 'on_headers' => function ($response) {
                if ((int) $response->getHeaderLine('Content-Length') > 3000000) {
                    throw new RuntimeException('Feed is too large.');
                }
            }, 'progress' => function ($total, $downloaded) {
                if ($downloaded > 3000000) {
                    throw new RuntimeException('Feed is too large.');
                }
            }])->get($url);
        if ($response->status() >= 300 && $response->status() < 400) {
            throw new RuntimeException('Feed redirected. Save its final HTTPS URL.');
        }
        $response->throw();
        if (strlen($response->body()) > 3000000) {
            throw new RuntimeException('Feed is too large.');
        }

        return $response->body();
    }
}
