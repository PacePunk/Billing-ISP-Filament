<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice; 
use App\Models\Subscription;
use App\Services\MikrotikService; 
use Illuminate\Support\Facades\Log;

class PaperWebhookController extends Controller
{
    public function handle(Request $request)
    {
       
        $data = $request->all();
        
        Log::info('Webhook Paper.id diterima:', $data);

        $event = $data['event'] ?? '';
        $payload = $data['payload'] ?? [];
        $status = $payload['status'] ?? '';

        if ($event == 'payment.changed' && $status == 'PAID') {
            
            $nomorInvoice = $payload['invoice_number']; 

            $localInvoice = Invoice::where('invoice_number', $nomorInvoice)->first();

            if ($localInvoice) {
                $localInvoice->update(['status' => 'paid', 'paid_at' => now()]);
                
                $subscription = Subscription::find($localInvoice->subscription_id);
                if ($subscription) {
                    $subscription->update(['status' => 'active']); // Nyalakan status user
                    
                    try {

                        MikrotikService::setProfile(
                            $subscription->router,
                            $subscription->mikrotik_username,
                            $subscription->package->mikrotik_profile 
                        );
                        Log::info("Internet untuk user {$subscription->mikrotik_username} berhasil dinyalakan.");
                    } catch (\Exception $e) {
                        Log::error("Gagal nyalakan Mikrotik: " . $e->getMessage());
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}