# FAZ 32 — AI Engine Özeti

## Kapsam
OpenAI’yi servis katmanı arkasına alan AI Engine temeli. Varsayılan `heuristic` sürücü API anahtarı gerektirmez; testler offline çalışır.

## Çıktılar
- `config/ai.php`, `AI_DRIVER` / `OPENAI_*` env
- `AiClientInterface` + `HeuristicAiClient` + `OpenAiClient`
- `ai_generations` tablosu + `AiEngineService` / `PromptBuilder`
- Web: `/companies/{company}/ai`
- Analiz sonucundan “AI Özet” aksiyonu
- Yetkiler: `ai.view` / `ai.generate`
- Feature test: `AiEngineTest`

## MVP yetenekleri
1. **Belge taslağı** — firma profili + şablon → taslak metin
2. **Bulgu özeti** — analiz findings → danışman özeti

## İş kuralları
- Prompt bağlamında vergi no / MERSİS / e-posta / telefon yok; şablon gövdesinden hassas satırlar temizlenir
- `openai` seçili ama key yoksa otomatik `heuristic`’e düşer
- Üretimler audit log’a yazılır

## Sonraki faz
FAZ 33 — API
