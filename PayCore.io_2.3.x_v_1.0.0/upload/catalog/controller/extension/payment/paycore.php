<?php
class ControllerExtensionPaymentPaycore extends Controller {
    private $order;
    private $log;
    private $data = array();
    private static $ACTION = 'http://checkout.dev.paycore.io';
    private static $IPN_URl = 'extension/payment/paycore/status';
    private static $RETURN_URL = 'checkout/success';
    private static $LOG_OFF = 0;
    private static $LOG_SHORT = 1;
    private static $LOG_FULL = 2;
    private static $ORDER_STATUSES = [
        'success'  => 'paycore_order_status_id',
        'pending'  => 'paycore_order_confirm_status_id',
        'canceled' => 'paycore_order_canceled_status_id',
        'failure'  => 'paycore_order_fail_status_id',
        'expired'  => 'paycore_order_expired_status_id',
    ];

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('extension/payment/paycore');
    }

    public function index() {
        $langdata = $this->config->get('paycore_langdata');
        $this->_setData(
            array(
                 'button_confirm',
                 'instruction'  => nl2br($langdata[$this->config->get('config_language_id')]['instruction']),
                 'action'       => self::$ACTION,
                 'parameters'   => $this->makeForm()
            )
        );

        return $this->load->view('extension/payment/paycore', $this->data);
    }

    public function status() {
        $this->logWrite('StatusURL: ', self::$LOG_SHORT);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        $body = json_decode(file_get_contents('php://input', 'rb'), true);

        if (!isset($body['data']) && !isset($body['signature'])) {
            $this->logWrite('  POST BODY:' . var_export($body, true), self::$LOG_FULL);
            $this->logWrite('BODY does not consist data or signature', self::$LOG_FULL);

            return;
        }

        $dataString = $body['data'];
        $receivedSignature = $body['signature'];

        // DON'T delete this block, be careful of fraud!!!
        if (!$this->securityOrderCheck($dataString, $receivedSignature)) {
            $this->logWrite('Error: Signature check failed. WARNING be careful of fraud', self::$LOG_SHORT);

            return;
        }

        $data = self::getDecodedData($dataString);

        $orderId = $data['reference'];

        $this->load->model('checkout/order');
        $this->order = $this->model_checkout_order->getOrder($orderId);

        if (empty($this->order)) {
            $this->logWrite('ERROR: ' . sprintf($this->language->get('text_error_order_not_found'), $orderId), self::$LOG_SHORT);

            return;
        }

        if ($data['state'] !== 'success' && $this->order['order_status_id'] === $this->config->get(self::$ORDER_STATUSES['success'])) {
            return;
        }

        $orderStatusAlias = self::$ORDER_STATUSES[$data['state']];

        if (null !== $orderStatusAlias) {
            $this->model_checkout_order->addOrderHistory($this->order['order_id'],
                $this->config->get($orderStatusAlias),
                sprintf($this->language->get('text_comment_payment_status'), $this->order['order_id'], $data['state']),
                true);
        } else {
            $this->model_checkout_order->addOrderHistory($this->order['order_id'],
                $this->config->get(self::$ORDER_STATUSES['failure']),
                sprintf($this->language->get('text_comment_payment_status'), $this->order['order_id'], $data['state']),
                true);
        }
    }

    protected function getOrderStatusId() {

    }

    public function confirm() {
        if (!empty($this->session->data['order_id']) && $this->config->get('paycore_order_confirm_status_id') && ($this->session->data['payment_method']['code'] == 'paycore')) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('paycore_order_confirm_status_id'));
        }
    }

    protected function makeForm($order_id = false) {
        $this->load->model('checkout/order');
        if (!$order_id ) {
          if (isset($this->session->data['order_id'])) {
            $order_id  = $this->session->data['order_id'];
          } else {
            $this->logWrite('Error: Unsupported Checkout Extension', self::$LOG_SHORT);
            die($this->language->get('error_fail_checkout_extension'));
          }
        }
        $order_info = $this->model_checkout_order->getOrder($order_id);

        $currency = $this->session->data['currency'];
        
        $paymentAmount       = number_format($this->currency->convert($order_info['total'], $this->config->get('config_currency'), $currency), 2, '.', '');
        $publicKey           = $this->getPaycorePublicKey();
        $paymentDescription  = sprintf($this->language->get('text_payment_desc'), $order_info['order_id']);

        $data = array(
            'amount'      => floatval($paymentAmount) * 100,
            'public_key'  => $publicKey,
            'currency'    => $currency,
            'description' => $paymentDescription,
            'reference'   => $order_info['order_id'],
            'ipn_url'     => $this->url->link(self::$IPN_URl),
            'return_url'  => $this->url->link(self::$RETURN_URL),
        );

        $dataString = self::encodeData($data);
        $signature = $this->generateDataSignature($data);

        $params = array(
            'data'      => $dataString,
            'signature' => $signature,
        );

        $this->logWrite('Make payment form: ', self::$LOG_SHORT);
        $this->logWrite('  DATA: ' . var_export($params, true), self::$LOG_FULL);

        return $params;
    }

    protected function _setData($values) {
        $this->data = array();
        foreach ($values as $key => $value) {
            if (is_int($key)) {
                $this->data[$value] = $this->language->get($value);
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    protected function logWrite($message, $type) {
        switch ($this->config->get('paycore_log')) {
            case self::$LOG_OFF:
                return;
            case self::$LOG_SHORT:
                if ($type == self::$LOG_FULL) {
                    return;
                }
        }

        if (!$this->log) {
            $this->log = new Log($this->config->get('paycore_log_filename'));
        }
        $this->log->Write($message);
    }

    protected function getPaycorePublicKey() {
        return $this->config->get('paycore_test_mode') ?
            $this->config->get('paycore_test_public_key') :
            $this->config->get('paycore_public_key');
    }
    protected function getPaycoreSecretKey() {
        return $this->config->get('paycore_test_mode') ?
            $this->config->get('paycore_test_secret_key') :
            $this->config->get('paycore_secret_key');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getDecodedData($data)
    {
        return json_decode(base64_decode($data), true, 1024);
    }

    /**
     * @param $data
     * @return string
     */
    public static function encodeData($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * @param $data
     * @param $receivedSignature
     * @return bool
     */
    public function securityOrderCheck($data, $receivedSignature)
    {
        $secretKey = $this->getPaycoreSecretKey();
        $generatedSignature = base64_encode(sha1( $secretKey . $data . $secretKey, 1));

        return $receivedSignature === $generatedSignature;
    }

    /**
     * @param $data array
     * @return string
     */
    public function generateDataSignature($data)
    {
        $secretKey = $this->getPaycoreSecretKey();
        $encodedData = self::encodeData($data);
        $signature = self::generateStringSignature($secretKey . $encodedData . $secretKey);

        return $signature;
    }

    /**
     * @param $string
     * @return string
     */
    public static function generateStringSignature($string)
    {
        return base64_encode(sha1($string, 1));
    }
}
?>