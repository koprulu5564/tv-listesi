import requests
import time
import sys

# Sabit User-Agent (daha kararlı çalışır)
USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"

def fetch_m3u(url, max_retries=3):
    headers = {
        'User-Agent': USER_AGENT,
        'Accept': '*/*',
        'Connection': 'keep-alive'
    }

    for attempt in range(max_retries):
        try:
            print(f"🔍 Deneme {attempt + 1}/{max_retries}")
            response = requests.get(url, headers=headers, timeout=15)
            
            if response.status_code == 200 and response.text.startswith("#EXTM3U"):
                return response.text
            else:
                print(f"⚠️ HTTP {response.status_code} | Yanıt: {response.text[:100]}...")
        except Exception as e:
            print(f"❌ Hata: {str(e)}")
        
        if attempt < max_retries - 1:
            time.sleep(5)  # 5 saniye bekle

    return None

if __name__ == "__main__":
    M3U_URL = "http://noxcon.cfd/get.php?username=mevlut73&password=73mevlut&type=m3u_plus"
    
    print("📡 M3U listesi alınıyor...")
    m3u_content = fetch_m3u(M3U_URL)
    
    if m3u_content:
        with open("tv_listesi.m3u", "w", encoding="utf-8") as f:
            f.write(m3u_content)
        print("✅ tv_listesi.m3u başarıyla oluşturuldu!")
        sys.exit(0)
    else:
        print("❌ M3U alınamadı! Lütfen:")
        print("- URL'yi tarayıcıda test edin")
        print("- Sunucu yöneticisiyle iletişime geçin")
        sys.exit(1)
