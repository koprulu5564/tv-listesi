<?php
// 1. API'den veriyi çek (Token 24 saat geçerli olsa bile otomatik yenilenecek)
$url = "https://core-api.kablowebtv.com/api/channels";
$headers = [
    "User-Agent: Mozilla/5.0",
    "Authorization: Bearer " . getFreshToken(),
    "Accept: application/json"
];

$data = json_decode(file_get_contents($url, false, stream_context_create([
    'http' => ['header' => $headers]
])), true);

// 2. M3U dosyasını oluştur
$m3u = "#EXTM3U\n";
foreach ($data['Data']['AllChannels'] as $channel) {
    if (!empty($channel['StreamData']['HlsStreamUrl'])) {
        $m3u .= sprintf("#EXTINF:-1 tvg-id=\"%s\" group-title=\"%s\",%s\n%s\n",
            $channel['Id'],
            $channel['Categories'][0]['Name'] ?? 'Genel',
            $channel['Name'],
            $channel['StreamData']['HlsStreamUrl']
        );
    }
}

// 3. Dosyaya yaz
file_put_contents('playlist.m3u', $m3u);

// Token yenileme fonksiyonu (Örnek)
function getFreshToken() {
    // Bu kısmı kendi token altyapınıza göre düzenleyin
    return "YENİ_ALINAN_TOKEN";
}
?>
