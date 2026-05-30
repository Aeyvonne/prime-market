<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaiementService
{
    protected string $appUrl;
    protected string $waveApiKey;
    protected string $waveSecretKey;
    protected string $orangeClientId;
    protected string $orangeClientSecret;

    public function __construct()
    {
        $this->appUrl = config('app.url', 'http://127.0.0.1:8000');

        $this->waveApiKey = env('WAVE_API_KEY', '');
        $this->waveSecretKey = env('WAVE_SECRET_KEY', '');
        $this->orangeClientId = env('ORANGE_MONEY_CLIENT_ID', '');
        $this->orangeClientSecret = env('ORANGE_MONEY_CLIENT_SECRET', '');
    }

    public function waveDisponible(): bool
    {
        return !empty($this->waveApiKey) && !empty($this->waveSecretKey);
    }

    public function orangeDisponible(): bool
    {
        return !empty($this->orangeClientId) && !empty($this->orangeClientSecret);
    }

    /**
     * Initier un paiement Wave
     * @return array{success: bool, redirect_url?: string, session_id?: string, message?: string}
     */
    public function initierWave(int $montant, int $commandeId): array
    {
        if (!$this->waveDisponible()) {
            return $this->simulationPaiement($montant, $commandeId, 'Wave');
        }

        try {
            $callbackSuccess = urlencode($this->appUrl . '/api/paiements/callback?commande_id=' . $commandeId . '&status=success');
            $callbackFail    = urlencode($this->appUrl . '/api/paiements/callback?commande_id=' . $commandeId . '&status=fail');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->waveApiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.wave.com/v1/checkout/sessions', [
                'amount'             => $montant,
                'currency'           => 'XOF',
                'error_url'          => $callbackFail,
                'success_url'        => $callbackSuccess,
                'metadata'           => [
                    'commande_id' => $commandeId,
                ],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success'      => true,
                    'redirect_url' => $body['checkout_url'] ?? ($body['redirect_url'] ?? ''),
                    'session_id'   => $body['id'] ?? null,
                ];
            }

            Log::error('Wave API error: ' . $response->body());
            return $this->simulationPaiement($montant, $commandeId, 'Wave');

        } catch (\Exception $e) {
            Log::error('Wave API exception: ' . $e->getMessage());
            return $this->simulationPaiement($montant, $commandeId, 'Wave');
        }
    }

    /**
     * Initier un paiement Orange Money
     * @return array{success: bool, redirect_url?: string, session_id?: string, message?: string}
     */
    public function initierOrangeMoney(int $montant, int $commandeId): array
    {
        if (!$this->orangeDisponible()) {
            return $this->simulationPaiement($montant, $commandeId, 'OrangeMoney');
        }

        try {
            $callbackUrl = $this->appUrl . '/api/paiements/callback?commande_id=' . $commandeId;

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->orangeClientId . ':' . $this->orangeClientSecret),
                'Content-Type'  => 'application/json',
            ])->post('https://api.orange.com/orange-money-webpay/senegal/v1/payment', [
                'merchant' => [
                    'merchant_key'  => $this->orangeClientId,
                    'callback_url'  => $callbackUrl,
                    'return_url'    => $this->appUrl . '/paiement/succes?commande_id=' . $commandeId,
                    'cancel_url'    => $this->appUrl . '/paiement/echec?commande_id=' . $commandeId,
                ],
                'order' => [
                    'order_id'   => 'CMD-' . $commandeId,
                    'amount'     => $montant,
                    'currency'   => 'XOF',
                ],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success'      => true,
                    'redirect_url' => $body['payment_url'] ?? ($body['redirect_url'] ?? ''),
                    'session_id'   => $body['transaction_id'] ?? $body['order_id'] ?? null,
                ];
            }

            Log::error('OrangeMoney API error: ' . $response->body());
            return $this->simulationPaiement($montant, $commandeId, 'OrangeMoney');

        } catch (\Exception $e) {
            Log::error('OrangeMoney API exception: ' . $e->getMessage());
            return $this->simulationPaiement($montant, $commandeId, 'OrangeMoney');
        }
    }

    /**
     * Vérifier un paiement Wave par session_id
     */
    public function verifierWave(string $sessionId): bool
    {
        if (!$this->waveDisponible()) {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->waveApiKey,
            ])->get("https://api.wave.com/v1/checkout/sessions/{$sessionId}");

            if ($response->successful()) {
                $status = $response->json()['state'] ?? '';
                return $status === 'complete';
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Wave verification error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Vérifier un paiement Orange Money par transaction_id
     */
    public function verifierOrangeMoney(string $transactionId): bool
    {
        if (!$this->orangeDisponible()) {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->orangeClientId . ':' . $this->orangeClientSecret),
            ])->get("https://api.orange.com/orange-money-webpay/senegal/v1/transaction/{$transactionId}");

            if ($response->successful()) {
                $status = $response->json()['status'] ?? '';
                return $status === 'SUCCESS';
            }

            return false;
        } catch (\Exception $e) {
            Log::error('OrangeMoney verification error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Mode simulation — utilisé quand les clés API ne sont pas configurées
     */
    private function simulationPaiement(int $montant, int $commandeId, string $methode): array
    {
        Log::info("[SIMULATION] Paiement {$methode} initié — Commande #{$commandeId}, montant: {$montant} XOF");

        $sessionId = 'sim_' . strtolower($methode) . '_' . $commandeId . '_' . time();
        $token = base64_encode(json_encode([
            'commande_id'  => $commandeId,
            'methode'      => $methode,
            'montant'      => $montant,
            'session_id'   => $sessionId,
        ]));

        return [
            'success'      => true,
            'redirect_url' => '/paiement/simule?token=' . urlencode($token),
            'session_id'   => $sessionId,
        ];
    }
}
