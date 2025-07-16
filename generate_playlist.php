<?php
header('Content-Type: text/plain; charset=utf-8');

function getFinalUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $response = curl_exec($ch);
    preg_match('/Location: (.*)/i', $response, $matches);
    curl_close($ch);
    return $matches[1] ?? $url;
}

$channels = [
    'TRT1' => 'https://tv-veri.kablo.com/live/turksat_sub3/trt1_stream/playlist.m3u8',
    'ShowTV' => 'https://tv-veri.kablo.com/live/turksat_sub3/showtv_stream/playlist.m3u8'
];

$m3u = "#EXTM3U\n";
foreach ($channels as $name => $url) {
    $finalUrl = getFinalUrl($url);
    $m3u .= "#EXTINF:-1,$name\n";
    $m3u .= "$finalUrl\n";
}

file_put_contents('playlist.m3u', $m3u);
echo "Playlist oluşturuldu!";
?>
