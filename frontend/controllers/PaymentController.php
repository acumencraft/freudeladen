<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use common\models\Order;
use common\models\Cart;

class PaymentController extends Controller
{
    private $stripeSecretKey;
    private $stripePublishableKey;
    private $paypalClientId;
    private $paypalClientSecret;
    private $paypalMode;
    
    public function init()
    {
        parent::init();
        
        // Initialize payment gateway credentials from params
        $this->stripeSecretKey = Yii::$app->params['stripe']['secret_key'] ?? 'sk_test_51234567890abcdef';
        $this->stripePublishableKey = Yii::$app->params['stripe']['publishable_key'] ?? 'pk_test_51234567890abcdef';
        $this->paypalClientId = Yii::$app->params['paypal']['client_id'] ?? 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R';
        $this->paypalClientSecret = Yii::$app->params['paypal']['client_secret'] ?? 'EGnHDxD_qRPdaLdHgGWiw6lLt9xtGe0Lw-tgH2BvCpVwKOiX4pKt6wQ2aF3YxGx4';
        $this->paypalMode = Yii::$app->params['paypal']['mode'] ?? 'sandbox';
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['stripe', 'paypal', 'bank-transfer', 'success', 'cancel', 'bank-instructions', 'webhook'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'webhook' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Process Stripe payment
     */
    public function actionStripe($order_id = null)
    {
        $request = Yii::$app->request;
        $orderId = $order_id ?: $request->post('order_id');
        
        if (!$orderId) {
            Yii::$app->session->setFlash('error', 'Ungültige Bestellnummer.');
            return $this->redirect(['cart/checkout']);
        }
        
        $order = Order::findOne($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        try {
            // For demo purposes, simulate successful payment
            // In production, integrate with actual Stripe API
            if (YII_ENV_DEV) {
                // Simulate payment processing
                $order->payment_status = 'paid';
                $order->payment_method = 'stripe';
                $order->save();
                
                // Clear cart
                $cart = new Cart();
                $cart->clear();
                
                Yii::$app->session->setFlash('success', 'Zahlung erfolgreich verarbeitet!');
                return $this->redirect(['payment/success', 'id' => $order->id]);
            } else {
                // Production Stripe integration would go here
                /*
                require_once Yii::getAlias('@vendor/stripe/stripe-php/init.php');
                \Stripe\Stripe::setApiKey($this->stripeSecretKey);
                
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Bestellung #' . $order->id,
                            ],
                            'unit_amount' => $order->total_amount * 100, // Stripe uses cents
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => Yii::$app->urlManager->createAbsoluteUrl(['payment/success', 'id' => $order->id]),
                    'cancel_url' => Yii::$app->urlManager->createAbsoluteUrl(['payment/cancel', 'id' => $order->id]),
                ]);
                
                return $this->redirect($session->url);
                */
                throw new \Exception('Stripe integration not configured for production.');
            }
        } catch (\Exception $e) {
            Yii::error('Stripe payment error: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Fehler bei der Zahlungsverarbeitung: ' . $e->getMessage());
            return $this->redirect(['payment/cancel', 'id' => $order->id]);
        }
    }

    /**
     * Process PayPal payment
     */
    public function actionPaypal($order_id = null)
    {
        $request = Yii::$app->request;
        $orderId = $order_id ?: $request->post('order_id');
        
        if (!$orderId) {
            Yii::$app->session->setFlash('error', 'Ungültige Bestellnummer.');
            return $this->redirect(['cart/checkout']);
        }
        
        $order = Order::findOne($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        try {
            // For demo purposes, simulate successful payment
            // In production, integrate with actual PayPal API
            if (YII_ENV_DEV) {
                // Simulate payment processing
                $order->payment_status = 'paid';
                $order->payment_method = 'paypal';
                $order->save();
                
                // Clear cart
                $cart = new Cart();
                $cart->clear();
                
                Yii::$app->session->setFlash('success', 'PayPal-Zahlung erfolgreich verarbeitet!');
                return $this->redirect(['payment/success', 'id' => $order->id]);
            } else {
                // Production PayPal integration would go here
                /*
                require_once Yii::getAlias('@vendor/paypal/rest-api-sdk-php/autoload.php');
                
                $apiContext = new \PayPal\Rest\ApiContext(
                    new \PayPal\Auth\OAuthTokenCredential(
                        $this->paypalClientId,
                        $this->paypalClientSecret
                    )
                );
                $apiContext->setConfig(['mode' => $this->paypalMode]);
                
                $payer = new \PayPal\Api\Payer();
                $payer->setPaymentMethod('paypal');
                
                $amount = new \PayPal\Api\Amount();
                $amount->setTotal($order->total_amount);
                $amount->setCurrency('EUR');
                
                $transaction = new \PayPal\Api\Transaction();
                $transaction->setAmount($amount);
                $transaction->setDescription('Bestellung #' . $order->id);
                
                $redirectUrls = new \PayPal\Api\RedirectUrls();
                $redirectUrls->setReturnUrl(Yii::$app->urlManager->createAbsoluteUrl(['payment/success', 'id' => $order->id]));
                $redirectUrls->setCancelUrl(Yii::$app->urlManager->createAbsoluteUrl(['payment/cancel', 'id' => $order->id]));
                
                $payment = new \PayPal\Api\Payment();
                $payment->setIntent('sale');
                $payment->setPayer($payer);
                $payment->setTransactions([$transaction]);
                $payment->setRedirectUrls($redirectUrls);
                
                $payment->create($apiContext);
                
                $approvalUrl = $payment->getApprovalLink();
                return $this->redirect($approvalUrl);
                */
                throw new \Exception('PayPal integration not configured for production.');
            }
        } catch (\Exception $e) {
            Yii::error('PayPal payment error: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Fehler bei der PayPal-Zahlung: ' . $e->getMessage());
            return $this->redirect(['payment/cancel', 'id' => $order->id]);
        }
    }

    /**
     * Process bank transfer payment
     */
    public function actionBankTransfer($order_id = null)
    {
        $request = Yii::$app->request;
        $orderId = $order_id ?: $request->post('order_id');
        
        if (!$orderId) {
            Yii::$app->session->setFlash('error', 'Ungültige Bestellnummer.');
            return $this->redirect(['cart/checkout']);
        }
        
        $order = Order::findOne($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        try {
            // Update order with bank transfer payment method
            $order->payment_method = 'bank_transfer';
            $order->payment_status = 'pending';
            $order->save();
            
            // Clear cart
            $cart = new Cart();
            $cart->clear();
            
            Yii::$app->session->setFlash('success', 'Bestellung aufgegeben! Bitte folgen Sie den Überweisungsanweisungen.');
            return $this->redirect(['payment/bank-instructions', 'id' => $order->id]);
        } catch (\Exception $e) {
            Yii::error('Bank transfer processing error: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Fehler beim Verarbeiten der Banküberweisung.');
            return $this->redirect(['cart/checkout']);
        }
    }

    /**
     * Payment success page
     */
    public function actionSuccess($id)
    {
        $order = Order::findOne($id);
        if (!$order) {
            throw new NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        return $this->render('success', [
            'order' => $order,
        ]);
    }

    /**
     * Payment cancel page
     */
    public function actionCancel($id = null)
    {
        $order = null;
        if ($id) {
            $order = Order::findOne($id);
        }
        
        return $this->render('cancel', [
            'order' => $order,
        ]);
    }

    /**
     * Bank transfer instructions page
     */
    public function actionBankInstructions($id)
    {
        $order = Order::findOne($id);
        if (!$order) {
            throw new NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        return $this->render('bank-instructions', [
            'order' => $order,
        ]);
    }

    /**
     * Webhook endpoint for payment notifications
     */
    public function actionWebhook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $payload = Yii::$app->request->getRawBody();
            $headers = Yii::$app->request->headers;
            
            // Handle Stripe webhooks
            if ($headers->has('stripe-signature')) {
                return $this->handleStripeWebhook($payload, $headers->get('stripe-signature'));
            }
            
            // Handle PayPal webhooks
            if ($headers->has('paypal-transmission-id')) {
                return $this->handlePaypalWebhook($payload, $headers);
            }
            
            throw new BadRequestHttpException('Unknown webhook source');
            
        } catch (\Exception $e) {
            Yii::error('Webhook error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle Stripe webhook
     */
    private function handleStripeWebhook($payload, $signature)
    {
        // In production, verify webhook signature
        // For demo, just log the event
        Yii::info('Stripe webhook received: ' . $payload);
        return ['status' => 'success'];
    }

    /**
     * Handle PayPal webhook
     */
    private function handlePaypalWebhook($payload, $headers)
    {
        // In production, verify webhook signature
        // For demo, just log the event
        Yii::info('PayPal webhook received: ' . $payload);
        return ['status' => 'success'];
    }
}
