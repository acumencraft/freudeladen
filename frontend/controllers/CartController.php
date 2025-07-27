<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use common\models\Cart;
use common\models\Product;
use common\models\ProductVariant;

/**
 * Cart controller for the frontend application
 */
class CartController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['POST'],
                    'update' => ['POST'],
                    'remove' => ['POST'],
                    'clear' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Before action - handle CSRF for AJAX requests
     */
    public function beforeAction($action)
    {
        // For AJAX cart operations, ensure session is started and CSRF is validated
        if (Yii::$app->request->isAjax && in_array($action->id, ['add', 'update', 'remove', 'clear'])) {
            // Ensure session is started
            Yii::$app->session->open();
            
            // Validate CSRF token
            if (!Yii::$app->request->validateCsrfToken()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = [
                    'success' => false,
                    'message' => 'CSRF validation failed'
                ];
                return false;
            }
        }
        
        return parent::beforeAction($action);
    }

    /**
     * Display cart
     */
    public function actionIndex()
    {
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        $cartItems = Cart::getCartItems($sessionId, $userId);
        $cartTotal = Cart::getCartTotal($sessionId, $userId);
        $cartCount = Cart::getCartCount($sessionId, $userId);

        return $this->render('index_simple', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Add item to cart
     */
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $productId = Yii::$app->request->post('product_id');
        $quantity = (int) Yii::$app->request->post('quantity', 1);
        $variantId = Yii::$app->request->post('variant_id');
        
        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID is required'];
        }
        
        $product = Product::findOne(['id' => $productId, 'status' => Product::STATUS_ACTIVE]);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($variantId) {
            $variant = ProductVariant::findOne(['id' => $variantId, 'product_id' => $productId]);
            if (!$variant) {
                return ['success' => false, 'message' => 'Product variant not found'];
            }
            
            if (!$variant->isInStock()) {
                return ['success' => false, 'message' => 'Product variant is out of stock'];
            }
        } else {
            if (!$product->isInStock()) {
                return ['success' => false, 'message' => 'Product is out of stock'];
            }
        }
        
        $sessionId = Yii::$app->session->getId();
        
        // Ensure session is started for cart operations
        if (empty($sessionId)) {
            Yii::$app->session->open();
            $sessionId = Yii::$app->session->getId();
        }
        
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        if (Cart::addItem($sessionId, $productId, $quantity, $variantId, $userId)) {
            $cartCount = Cart::getCartCount($sessionId, $userId);
            return [
                'success' => true, 
                'message' => 'Product added to cart',
                'cartCount' => $cartCount
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to add product to cart'];
    }

    /**
     * Update cart item quantity
     */
    public function actionUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $itemId = Yii::$app->request->post('item_id');
        $quantity = (int) Yii::$app->request->post('quantity', 1);
        
        if (!$itemId || $quantity < 1) {
            return ['success' => false, 'message' => 'Invalid parameters'];
        }
        
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        $cartItem = Cart::find()
            ->where(['id' => $itemId, 'session_id' => $sessionId])
            ->one();
            
        if (!$cartItem) {
            return ['success' => false, 'message' => 'Cart item not found'];
        }
        
        $cartItem->quantity = $quantity;
        
        if ($cartItem->save()) {
            $cartTotal = Cart::getCartTotal($sessionId, $userId);
            $cartCount = Cart::getCartCount($sessionId, $userId);
            
            return [
                'success' => true,
                'message' => 'Cart updated',
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount,
                'itemTotal' => number_format($cartItem->getTotalPrice(), 2)
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to update cart'];
    }

    /**
     * Remove item from cart
     */
    public function actionRemove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $itemId = Yii::$app->request->post('item_id');
        
        if (!$itemId) {
            return ['success' => false, 'message' => 'Item ID is required'];
        }
        
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        $cartItem = Cart::find()
            ->where(['id' => $itemId, 'session_id' => $sessionId])
            ->one();
            
        if (!$cartItem) {
            return ['success' => false, 'message' => 'Cart item not found'];
        }
        
        if ($cartItem->delete()) {
            $cartTotal = Cart::getCartTotal($sessionId, $userId);
            $cartCount = Cart::getCartCount($sessionId, $userId);
            
            return [
                'success' => true,
                'message' => 'Item removed from cart',
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to remove item from cart'];
    }

    /**
     * Clear cart
     */
    public function actionClear()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        if (Cart::clearCart($sessionId, $userId)) {
            return [
                'success' => true,
                'message' => 'Cart cleared',
                'cartTotal' => '0.00',
                'cartCount' => 0
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to clear cart'];
    }

    /**
     * Get cart count (AJAX)
     */
    public function actionCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        $cartCount = Cart::getCartCount($sessionId, $userId);
        
        return [
            'success' => true,
            'count' => $cartCount
        ];
    }

    /**
     * Checkout process
     */
    public function actionCheckout()
    {
        $sessionId = Yii::$app->session->getId();
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        $cartItems = Cart::getCartItems($sessionId, $userId);
        
        if (empty($cartItems)) {
            Yii::$app->session->setFlash('error', 'Ihr Warenkorb ist leer.');
            return $this->redirect(['site/index']);
        }
        
        $model = new \common\models\Order();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                // Create order
                $model->customer_email = $model->customer_email;
                $model->customer_name = $model->customer_name;
                $model->customer_phone = $model->customer_phone;
                $model->shipping_address = $model->shipping_address;
                $model->billing_address = $model->billing_address ?: $model->shipping_address;
                $model->status = 'pending';
                $model->notes = $model->notes;
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                
                // Calculate totals
                $subtotal = 0;
                foreach ($cartItems as $item) {
                    $subtotal += $item->getPrice() * $item->quantity;
                }
                
                $model->subtotal = $subtotal;
                $model->tax_amount = round($subtotal * 0.19, 2); // 19% VAT
                $model->shipping_cost = 5.99; // Fixed shipping
                $model->total_amount = $model->subtotal + $model->tax_amount + $model->shipping_cost;
                
                if (!$model->save()) {
                    throw new \Exception('Failed to save order');
                }
                
                // Create order items
                foreach ($cartItems as $item) {
                    $orderItem = new \common\models\OrderItem();
                    $orderItem->order_id = $model->id;
                    $orderItem->product_id = $item->product_id;
                    $orderItem->product_variant_id = $item->variant_id;
                    $orderItem->quantity = $item->quantity;
                    $orderItem->unit_price = $item->getPrice();
                    $orderItem->total_price = $item->getPrice() * $item->quantity;
                    
                    if (!$orderItem->save()) {
                        throw new \Exception('Failed to save order item');
                    }
                }
                
                // Clear cart
                Cart::deleteAll([
                    'AND',
                    ['session_id' => $sessionId],
                    $userId ? ['user_id' => $userId] : ['user_id' => null]
                ]);
                
                $transaction->commit();
                
                Yii::$app->session->setFlash('success', 'Ihre Bestellung wurde erfolgreich aufgegeben. Bestellnummer: ' . $model->id);
                return $this->redirect(['confirmation', 'id' => $model->id]);
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Fehler beim Erstellen der Bestellung: ' . $e->getMessage());
            }
        }
        
        $cartTotal = Cart::getCartTotal($sessionId, $userId);
        
        return $this->render('checkout', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'model' => $model
        ]);
    }

    /**
     * Order confirmation page
     */
    public function actionConfirmation($id)
    {
        $order = \common\models\Order::findOne($id);
        
        if (!$order) {
            throw new \yii\web\NotFoundHttpException('Bestellung nicht gefunden.');
        }
        
        return $this->render('confirmation', [
            'order' => $order
        ]);
    }
}
