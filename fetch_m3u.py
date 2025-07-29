import requests
import random
import time
from urllib.parse import urlparse

# USER-AGENT veritabanı (rastgele seçilecek)
USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1"
]

# M3U URL'niz (örnek)
M3U_URL = "http://spy17.eu/get.php?username=yesil123&password=12345&type=m3u_plus"

def get_random_user_agent():
    return random.choice(USER_AGENTS)

def get_referer_from_url(url):
    parsed = urlparse(url)
    return f"{parsed.scheme}://{parsed.netloc}/"

def fetch_m3u(url, max_retries=3, timeout=10):
    headers = {
        'User-Agent': get_random_user_agent(),
        'Referer': get_referer_from_url(url)
    }
    
    for attempt in range(max_retries):
        try:
            print(f"🔄 Deneme {attempt + 1}/{max_retries}: {url}")
            response = requests.get(url, headers=headers, timeout=timeout)
            response.raise_for_status()  # HTTP hatalarını yakala
            
            if response.text.strip().startswith("#EXTM3U"):
                print("✅ M3U listesi başarıyla alındı!")
                return response.text
            else:
                print("❌ Hata: Geçerli bir M3U dosyası alınamadı!")
                return None
                
        except requests.exceptions.RequestException as e:
            print(f"❌ Hata: {e}")
            if attempt < max_retries - 1:
                wait_time = 2 ** attempt  # Exponential backoff
                print(f"⏳ {wait_time} saniye bekleniyor...")
                time.sleep(wait_time)
    
    return None

if __name__ == "__main__":
    m3u_content = fetch_m3u(M3U_URL)
    if m3u_content:
        with open("tv_channels.m3u", "w", encoding="utf-8") as f:
            f.write(m3u_content)
        print("📁 'tv_channels.m3u' dosyası oluşturuldu!")
    else:
        print("❌ M3U listesi alınamadı. Lütfen URL ve bağlantınızı kontrol edin.")
