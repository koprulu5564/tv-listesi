import requests

source_url = "https://mth.tc/kabloid"  # TEST BAĞLANTISI
output_file = "kablotv_processed.m3u"

try:
    response = requests.get(source_url, headers={"User-Agent": "Mozilla/5.0"})
    response.raise_for_status()  # HTTP hatalarını yakala
    
    with open(output_file, "w") as file:
        file.write(response.text)
    print("M3U listesi başarıyla oluşturuldu!")
except Exception as e:
    print(f"Hata: {e}")
    exit(1)  # Workflow'u durdur
