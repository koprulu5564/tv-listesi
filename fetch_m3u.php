<?php
$source_url = 'https://koprulu.liveblog365.com/kablotv.m3u';
$output_file = 'kablotv_processed.m3u';

// M3U içeriğini çek
$m3u_content = file_get_contents($source_url);

// Dosyaya yaz
file_put_contents($output_file, $m3u_content);

echo "M3U listesi başarıyla oluşturuldu!";
?>
