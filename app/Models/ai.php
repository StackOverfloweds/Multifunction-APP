<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Endpoint Ollama Lokal (offline)
    |--------------------------------------------------------------------------
    | Ollama jalan sebagai service terpisah di server yang sama (localhost),
    | jadi tidak butuh internet setelah model di-pull sekali.
    */
    'ollama_base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),

    /*
    |--------------------------------------------------------------------------
    | Model default
    |--------------------------------------------------------------------------
    | Harus sama persis dengan tag yang di-pull, mis:
    |   ollama pull deepseek-r1:7b
    |   ollama pull deepseek-coder-v2:16b   (untuk kasus coding)
    */
    'default_model' => env('AI_DEFAULT_MODEL', 'deepseek-r1:7b'),

    'request_timeout' => env('AI_REQUEST_TIMEOUT', 120), // detik, DeepSeek reasoning bisa lama

    'system_prompt' => env(
        'AI_SYSTEM_PROMPT',
        'Kamu adalah AI Engineer assistant di dalam sistem Multifunction App. Jawab singkat, jelas, dan teknis bila relevan.'
    ),

    'max_context_messages' => env('AI_MAX_CONTEXT_MESSAGES', 20), // jumlah pesan lampau yg dikirim sbg konteks
];
