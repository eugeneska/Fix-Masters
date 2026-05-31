<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTelegramSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.telegram', [
            'botToken' => SiteSetting::get('telegram_bot_token') ?: config('services.telegram.bot_token'),
            'chatId' => SiteSetting::get('telegram_chat_id') ?: config('services.telegram.chat_id'),
        ]);
    }

    public function update(UpdateTelegramSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        SiteSetting::set('telegram_bot_token', $data['telegram_bot_token'] ?? '');
        SiteSetting::set('telegram_chat_id', $data['telegram_chat_id'] ?? '');

        return redirect()
            ->route('admin.settings.telegram')
            ->with('status', 'Настройки Telegram сохранены.');
    }
}
