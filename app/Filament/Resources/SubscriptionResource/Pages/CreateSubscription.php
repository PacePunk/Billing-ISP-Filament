<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\MikrotikService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function afterCreate(): void
    {
        $subscription = $this->record;

        try {
            MikrotikService::createSecret(
                $subscription->router,
                $subscription->mikrotik_username,
                $subscription->mikrotik_password,
                $subscription->package->mikrotik_profile
            );

            Notification::make()
                ->title('Sukses Terhubung!')
                ->body('Data tersimpan di DB & User PPPoE berhasil dibuat di Mikrotik.')
                ->success()
                ->duration(5000)
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Peringatan: Gagal Konek Mikrotik')
                ->body($e->getMessage() . ' (Data tetap tersimpan di Database lokal)')
                ->warning()
                ->persistent() 
                ->send();
        }
    }
}