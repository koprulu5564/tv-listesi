#!/usr/bin/env python3
import requests
import sys
from datetime import datetime

# Ayarlar
SOURCE_URL = "https://mth.tc/kabloid"  # HTTPS zorunlu!
OUTPUT_FILE = "kablotv_processed.m3u"
TIMEOUT = 10  # Saniye
USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

def log_error(message):
    """Hataları error.log dosyasına yazar"""
    with open("error.log", "a") as f:
        f.write(f"[{datetime.now()}] {message}\n")

try:
    # İstek ayarları
    headers = {
        "User-Agent": USER_AGENT,
        "Accept": "text/plain,*/*"
    }

    print(f"🔄 {SOURCE_URL} adresine istek gönderiliyor...")
    response = requests.get(SOURCE_URL, headers=headers, timeout=TIMEOUT)
    response.raise_for_status()  # HTTP hataları için

    # İçerik kontrolü
    if not response.text.strip():
        raise ValueError("Boş yanıt alındı!")
    
    if "#EXTM3U" not in response.text[:20]:
        raise ValueError("Geçersiz M3U formatı!")

    # Dosyaya yaz
    with open(OUTPUT_FILE, "w") as f:
        f.write(response.text)
    
    print(f"✅ {OUTPUT_FILE} oluşturuldu ({len(response.text)} byte)")
    sys.exit(0)

except Exception as e:
    error_msg = f"❌ Hata: {str(e)}"
    print(error_msg)
    log_error(error_msg)
    sys.exit(1)
