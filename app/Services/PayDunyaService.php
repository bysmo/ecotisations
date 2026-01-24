<?php

namespace App\Services;

use App\Models\PayDunyaConfiguration;
use Illuminate\Support\Facades\Log;

class PayDunyaService
{
    protected $config;

    public function __construct()
    {
        $this->config = PayDunyaConfiguration::getActive();
        
        if (!$this->config || !$this->config->enabled) {
            throw new \Exception('PayDunya n\'est pas configuré ou activé.');
        }

        // Configuration de l'API PayDunya selon la documentation
        \Paydunya\Setup::setMasterKey($this->config->master_key);
        \Paydunya\Setup::setPublicKey($this->config->public_key);
        \Paydunya\Setup::setPrivateKey($this->config->private_key);
        \Paydunya\Setup::setToken($this->config->token);
        \Paydunya\Setup::setMode($this->config->mode); // 'test' ou 'live'

        // Configuration des informations du store
        \Paydunya\Checkout\Store::setName(config('app.name', 'Ecotisations'));
        \Paydunya\Checkout\Store::setTagline('Gestion des cotisations');
        \Paydunya\Checkout\Store::setWebsiteUrl(config('app.url'));
        
        // Configuration globale de l'URL de callback IPN si définie
        // Si non définie, on utilisera l'URL par défaut lors de la création de la facture
        if ($this->config->ipn_url) {
            \Paydunya\Checkout\Store::setCallbackUrl($this->config->ipn_url);
        } else {
            // URL par défaut si non configurée
            $defaultCallbackUrl = config('app.url') . '/membre/paydunya/callback';
            \Paydunya\Checkout\Store::setCallbackUrl($defaultCallbackUrl);
        }
    }

    /**
     * Créer une facture PayDunya
     */
    public function createInvoice($data)
    {
        try {
            // Créer une nouvelle instance de facture
            $invoice = new \Paydunya\Checkout\CheckoutInvoice();

            // Ajouter l'article à la facture
            // Paramètres: nom, quantité, prix unitaire, prix total, description (optionnelle)
            $invoice->addItem(
                $data['item_name'],
                1,
                $data['amount'],
                $data['amount'],
                $data['description'] ?? ''
            );

            // Définir le montant total de la facture (obligatoire)
            $invoice->setTotalAmount($data['amount']);

            // Définir la description de la facture
            if (isset($data['description'])) {
                $invoice->setDescription($data['description']);
            }

            // Ajouter des données personnalisées
            $invoice->addCustomData('cotisation_id', $data['cotisation_id']);
            $invoice->addCustomData('membre_id', $data['membre_id']);
            
            // Ajouter engagement_id et type si présents
            if (isset($data['engagement_id'])) {
                $invoice->addCustomData('engagement_id', $data['engagement_id']);
            }
            if (isset($data['type'])) {
                $invoice->addCustomData('type', $data['type']);
            }

            // Configurer les URLs de redirection
            if (isset($data['cancel_url'])) {
                $invoice->setCancelUrl($data['cancel_url']);
            }
            if (isset($data['return_url'])) {
                $invoice->setReturnUrl($data['return_url']);
            }
            if (isset($data['callback_url'])) {
                $invoice->setCallbackUrl($data['callback_url']);
            }

            // Créer la facture sur les serveurs PayDunya
            if ($invoice->create()) {
                $invoiceUrl = $invoice->getInvoiceUrl();
                
                Log::info('PayDunya: Facture créée avec succès', [
                    'invoice_url' => $invoiceUrl,
                ]);

                return [
                    'success' => true,
                    'invoice_url' => $invoiceUrl,
                ];
            } else {
                Log::error('PayDunya: Erreur lors de la création de la facture', [
                    'response_text' => $invoice->response_text ?? 'Erreur inconnue',
                    'response_code' => $invoice->response_code ?? 'N/A',
                ]);

                return [
                    'success' => false,
                    'message' => $invoice->response_text ?? 'Erreur lors de la création de la facture',
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayDunya: Exception lors de la création de la facture', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le statut d'une facture
     */
    public function verifyInvoice($invoiceToken)
    {
        try {
            $invoice = new \Paydunya\Checkout\CheckoutInvoice();
            
            if ($invoice->confirm($invoiceToken)) {
                return [
                    'success' => true,
                    'status' => $invoice->getStatus(),
                    'data' => [
                        'status' => $invoice->getStatus(),
                        'total_amount' => $invoice->getTotalAmount(),
                        'customer' => [
                            'name' => $invoice->getCustomerInfo('name'),
                            'email' => $invoice->getCustomerInfo('email'),
                            'phone' => $invoice->getCustomerInfo('phone'),
                        ],
                        'custom_data' => [
                            'cotisation_id' => $invoice->getCustomData('cotisation_id'),
                            'membre_id' => $invoice->getCustomData('membre_id'),
                        ],
                    ],
                ];
            } else {
                Log::error('PayDunya: Erreur lors de la vérification de la facture', [
                    'token' => $invoiceToken,
                    'response_text' => $invoice->response_text ?? 'Erreur inconnue',
                    'response_code' => $invoice->response_code ?? 'N/A',
                ]);

                return [
                    'success' => false,
                    'message' => $invoice->response_text ?? 'Impossible de vérifier la facture',
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayDunya: Exception lors de la vérification de la facture', [
                'error' => $e->getMessage(),
                'token' => $invoiceToken,
            ]);
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }
}
