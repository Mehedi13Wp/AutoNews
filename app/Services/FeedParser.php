<?php

namespace App\Services;

use RuntimeException;

class FeedParser
{
    public function parse(string $xml): array
    {
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)) {
            throw new RuntimeException('XML entities are not supported.');
        }
        $before = libxml_use_internal_errors(true);
        try {
            $feed = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($before);
        }
        if ($feed === false) {
            throw new RuntimeException('This is not a valid RSS or Atom feed.');
        }
        $nodes = $feed->xpath('//*[local-name()="item" or local-name()="entry"]');
        $items = [];
        foreach (array_slice($nodes ?: [], 0, 50) as $node) {
            $get = function (string $name) use ($node) {
                $r = $node->xpath('./*[local-name()="'.$name.'"]');

                return isset($r[0]) ? (string) $r[0] : '';
            };
            $url = trim($get('link'));
            if (! $url) {
                foreach ($node->xpath('./*[local-name()="link"]') as $link) {
                    if (! isset($link['rel']) || (string) $link['rel'] === 'alternate') {
                        $url = (string) $link['href'];
                        break;
                    }
                }
            }
            $title = trim(strip_tags($get('title')));
            if (! $title || ! filter_var($url, FILTER_VALIDATE_URL) || ! in_array(parse_url($url, PHP_URL_SCHEME), ['https', 'http'])) {
                continue;
            }
            $image = null;
            foreach ($node->xpath('./*[local-name()="content" or local-name()="thumbnail" or local-name()="enclosure"]') as $media) {
                $candidate = (string) ($media['url'] ?? '');
                if (filter_var($candidate, FILTER_VALIDATE_URL) && in_array(parse_url($candidate, PHP_URL_SCHEME), ['https', 'http']) && (str_starts_with((string) ($media['type'] ?? ''), 'image/') || (string) ($media['medium'] ?? '') === 'image' || $media->getName() === 'thumbnail')) {
                    $image = $candidate;
                    break;
                }
            }
            $body = trim(html_entity_decode(strip_tags($get('encoded') ?: $get('content') ?: $get('description') ?: $get('summary')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $items[] = ['title' => $title, 'url' => $url, 'body' => mb_substr($body, 0, 20000), 'image' => $image, 'date' => $get('pubDate') ?: $get('published') ?: $get('updated')];
        }
        usort($items, fn ($a, $b) => (strtotime($b['date']) ?: 0) <=> (strtotime($a['date']) ?: 0));

        return $items;
    }
}
