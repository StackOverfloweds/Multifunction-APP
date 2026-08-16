<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\Auth;

class ConversationQuery
{
    public function list()
    {
        return Auth::user()->conversations()
            ->latest('updated_at')
            ->get();
    }

    /**
     * Setara bagian $activeConversation di AIEngineerController::index.
     * Filter role in(['user','assistant']) PENTING dipertahankan — paket
     * laravel/ai menyimpan row internal (mis. hasil moderasi/guardrail) di
     * tabel yang sama, kalau tidak difilter row itu ikut muncul sebagai
     * bubble chat aneh di frontend.
     */
    public function find($_, array $args)
    {
        return Auth::user()->conversations()
            ->whereKey($args['id'])
            ->with(['messages' => function ($query) {
                $query->whereIn('role', ['user', 'assistant'])
                    ->orderBy('created_at');
            }])
            ->first();
    }
}