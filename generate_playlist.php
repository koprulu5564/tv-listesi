<?php
header('Content-Type: text/plain; charset=utf-8');

// API'den veri çekme fonksiyonu (Token süresi kontrol edilmeli)
function fetchChannelData() {
    $url = "https://core-api.kablowebtv.com/api/channels";
    $headers = [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36",
        "Referer: https://tvheryerde.com",
        "Origin: https://tvheryerde.com",
        "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbnYiOiJMSVZFIiwiaXBiIjoiMCIsImNnZCI6IjA5M2Q3MjBhLTUwMmMtNDFlZC1hODBmLTJiODE2OTg0ZmI5NSIsImNzaCI6IlRSS1NUIiwiZGN0IjoiM0VGNzUiLCJkaSI6ImE2OTliODNmLTgyNmItNGQ5OS05MzYxLWM4YTMxMzIxOGQ0NiIsInNnZCI6Ijg5NzQxZmVjLTFkMzMtNGMwMC1hZmNkLTNmZGFmZTBiNmEyZCIsInNwZ2QiOiIxNTJiZDUzOS02MjIwLTQ0MjctYTkxNS1iZjRiZDA2OGQ3ZTgiLCJpY2giOiIwIiwiaWRtIjoiMCIsImlhIjoiOjpmZmZmOjEwLjAuMC4yMDYiLCJhcHYiOiIxLjAuMCIsImFibiI6IjEwMDAiLCJuYmYiOjE3NDUxNTI4MjUsImV4cCI6MTc0NTE1Mjg4NSwiaWF0IjoxNzQ1MTUyODI1fQ.OSlafRMxef4EjHG5t6TqfAQC7y05IiQjwwgf6yMUS9E"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_FAILONERROR, true); // Hataları yakala

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        file_put_contents('error.log', 'CURL Error: ' . curl_error($ch), FILE_APPEND);
        return false;
    }
    curl_close($ch);

    return $response;
}

// M3U oluşturma fonksiyonu (MOD kontrolü olmadan)
function generateM3U($apiData) {
    $data = json_decode($apiData, true);
    if (!$data || !$data['IsSucceeded'] || !isset($data['Data']['AllChannels'])) {
        file_put_contents('error.log', "API Invalid Response: " . print_r($data, true), FILE_APPEND);
        return false;
    }

    $m3uContent = "#EXTM3U\n";
    foreach ($data['Data']['AllChannels'] as $channel) {
        if (empty($channel['Name']) || empty($channel['StreamData']['HlsStreamUrl'])) {
            continue;
        }
        
        $group = $channel['Categories'][0]['Name'] ?? 'Genel';
        if ($group === "Bilgilendirme") continue;
        
        $m3uContent .= '#EXTINF:-1 tvg-logo="' . ($channel['PrimaryLogoImageUrl'] ?? '') . '" group-title="' . $group . '",' . $channel['Name'] . "\n";
        $m3uContent .= $channel['StreamData']['HlsStreamUrl'] . "\n"; // MOD olmadan direkt URL
    }

    return $m3uContent;
}

// Ana işlem
$apiResponse = fetchChannelData();
if ($apiResponse) {
    $m3uContent = generateM3U($apiResponse);
    if ($m3uContent !== false) {
        if (file_put_contents('playlist.m3u', $m3uContent) === false) {
            file_put_contents('error.log', "M3U write failed!", FILE_APPEND);
        }
    }
}

// Hata logunu kontrol et (Opsiyonel)
if (file_exists('error.log')) {
    echo file_get_contents('error.log');
}
?>
