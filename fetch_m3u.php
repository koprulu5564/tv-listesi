<?php
$source_url = 'https://koprulu.liveblog365.com/kablotv.m3u';
$output_file = 'kablotv_processed.m3u';

// CURL ile tarayıcı benzeri istek yap
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $source_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
$m3u_content = curl_exec($ch);
curl_close($ch);

// Veriyi dosyaya yaz
if ($m3u_content !== false) {
    file_put_contents($output_file, $m3u_content);
    echo "M3U listesi başarıyla oluşturuldu!";
} else {
    echo "Hata: M3U içeriği alınamadı!";
    exit(1); // Workflow'u durdur
}
?>
