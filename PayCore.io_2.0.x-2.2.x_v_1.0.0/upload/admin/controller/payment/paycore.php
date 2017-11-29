<?php
class ControllerPaymentPaycore extends Controller {
    private $error = array();
    private $version = '1.0.pc';
    const MAX_LAST_LOG_LINES = 500;
    const FILE_NAME_LOG = 'paycore.log';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('payment/paycore');
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function index() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->_trimData(array(
                 'paycore_public_key',
                 'paycore_secret_key',
                 'paycore_test_public_key',
                 'paycore_test_secret_key',
            ));

            $this->_replaceData(',', '.', array(
                 'paycore_minimal_order',
                 'paycore_maximal_order'
            ));

            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('paycore', $this->request->post);
            $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_title'));

            $this->response->redirect($this->makeUrl('extension/payment'));
        }

        $this->load->model('localisation/currency');
        $this->load->model('localisation/geo_zone');
        $this->load->model('localisation/language');
        $this->load->model('localisation/order_status');

        $permission = $this->validatePermission();
        if (!$permission ) {
            $this->error['warning'] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        }

        $server = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_CATALOG : HTTP_CATALOG;

        $data = $this->_setData(array(
            'heading_title',
            'button_save',
            'button_cancel',
            'button_clear',
            'tab_general',
            'tab_emails',
            'tab_settings',
            'tab_log',
            'tab_information',
            'lang',
            'text_confirm',
            'text_enabled',
            'text_disabled',
            'text_all_zones',
            'text_yes',
            'text_no',
            'text_info',
            'text_info_content',
            'text_parameters',
            'entry_geo_zone',
            'entry_status',
            'entry_sort_order',
            'entry_minimal_order',
            'entry_maximal_order',
            'entry_order_status',
            'entry_order_confirm_status',
            'entry_order_fail_status',
            'entry_order_canceled_status',
            'entry_order_expired_status',
            'entry_laterpay_mode',
            'entry_order_later_status',
            'entry_title',
            'entry_instruction',

            'entry_notify_customer_success',
            'entry_mail_customer_success_subject',
            'entry_mail_customer_success_content',
            'entry_notify_customer_fail',
            'entry_mail_customer_fail_subject',
            'entry_mail_customer_fail_content',
            'entry_notify_admin_success',
            'entry_mail_admin_success_subject',
            'entry_mail_admin_success_content',
            'entry_notify_admin_fail',
            'entry_mail_admin_fail_subject',
            'entry_mail_admin_fail_content',
            
            'entry_public_key',
            'entry_secret_key',
            'entry_test_public_key',
            'entry_test_secret_key',
            'entry_test_mode',
            'entry_lifetime',
            'entry_success_url',
            'entry_fail_url',
            'entry_pending_url',
            'entry_status_url',

            'entry_log',
            'entry_log_file',

            'placeholder_instruction',

            'help_minimal_order',
            'help_maximal_order',
            'help_order_confirm_status',
            'help_order_status',
            'help_order_fail_status',
            'help_order_canceled_status',
            'help_order_expired_status',
            'help_laterpay_mode',
            'help_order_later_status',
            'help_title',
            'help_instruction',

            'help_notify_customer_success',
            'help_mail_customer_success_subject',
            'help_mail_customer_success_content',
            'help_notify_customer_fail',
            'help_mail_customer_fail_subject',
            'help_mail_customer_fail_content',
            'help_notify_admin_success',
            'help_mail_admin_success_subject',
            'help_mail_admin_success_content',
            'help_notify_admin_fail',
            'help_mail_admin_fail_subject',
            'help_mail_admin_fail_content',

            'help_public_key',
            'help_secret_key',
            'help_test_public_key',
            'help_test_secret_key',
            'help_test_mode',
            'help_lifetime',
            'help_log_file'             => sprintf($this->language->get('help_log_file'), self::MAX_LAST_LOG_LINES),
            'help_log'                  => sprintf($this->language->get('help_log'), self::FILE_NAME_LOG),
            'title_default'             => explode(',', $this->language->get('heading_title')),
            'action'                    => $this->makeUrl('payment/paycore'),
            'cancel'                    => $this->makeUrl('extension/payment'),
            'clear_log'                 => $this->makeUrl('payment/paycore/clearLog'),
            'text_copyright'            => sprintf($this->language->get('text_copyright'), $this->language->get('heading_title')),
            'permission'                => $permission,
            'error_warning'             => isset($this->error['warning']) ? $this->error['warning'] : '',
            'error_public_key'          => isset($this->error['error_public_key']) ? $this->error['error_public_key'] : '',
            'error_secret_key'          => isset($this->error['error_secret_key']) ? $this->error['error_secret_key'] : '',
            'error_test_public_key'     => isset($this->error['error_test_public_key']) ? $this->error['error_test_public_key'] : '',
            'error_test_secret_key'     => isset($this->error['error_test_secret_key']) ? $this->error['error_test_secret_key'] : '',
            'version'                   => $this->version,
            'log_lines'                 => $this->readLastLines(DIR_LOGS . 'paycore.log', self::MAX_LAST_LOG_LINES),
            'log_filename'              => self::FILE_NAME_LOG,
            'geo_zones'                 => $this->model_localisation_geo_zone->getGeoZones(),
            'order_statuses'            => $this->model_localisation_order_status->getOrderStatuses(),
            'oc_languages'              => $this->model_localisation_language->getLanguages()
        ));

        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('common/dashboard'),
           'text'      => $this->language->get('text_home')
        );
        
        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('extension/payment'),
           'text'      => $this->language->get('text_extension')
        );
        
        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('payment/paycore'),
           'text'      => $this->language->get('heading_title')
        );

        $data['logs'] = array(
            '0' => $this->language->get('text_log_off'),
            '1' => $this->language->get('text_log_short'),
            '2' => $this->language->get('text_log_full')
        );

        $data['test_modes'] = array(
            '0' => $this->language->get('text_disabled'),
            '1' => $this->language->get('text_enabled'),
        );

        $data = array_merge($data, $this->_updateData(
            array(
                'paycore_geo_zone_id',
                'paycore_sort_order',
                'paycore_status',
                'paycore_minimal_order',
                'paycore_maximal_order',
                'paycore_order_status_id',
                'paycore_order_fail_status_id',
                'paycore_order_canceled_status_id',
                'paycore_order_expired_status_id',
                'paycore_order_confirm_status_id',
                'paycore_langdata',

                'paycore_public_key',
                'paycore_secret_key',
                'paycore_test_public_key',
                'paycore_test_secret_key',
                'paycore_test_mode',
                'paycore_currency',
                'paycore_lifetime',
                'paycore_log'
            ),
            array()
        ));

        $data = array_merge($data, $this->_setData(
            array(
                 'header'       => $this->load->controller('common/header'),
                 'column_left'  => $this->load->controller('common/column_left'),
                 'footer'       => $this->load->controller('common/footer')
            )
        ));

        $this->response->setOutput($this->load->view('payment/paycore.tpl', $data));
    }

    public function clearLog() {
      $json = array();

      if ($this->validatePermission()) {
          if (is_file(DIR_LOGS . self::FILE_NAME_LOG)) {
              @unlink(DIR_LOGS . self::FILE_NAME_LOG);
          }
          $json['success'] = $this->language->get('text_clear_log_success');
      } else {
          $json['error'] = $this->language->get('error_clear_log');
      }

      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->validatePermission()) {
            $this->error['warning'] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        } else {
            if ($this->request->post['paycore_test_mode']) {
                if (!isset($this->request->post['paycore_test_public_key']) || !trim($this->request->post['paycore_test_public_key'])) {
                    $this->error['warning'] = $this->error['error_test_public_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_test_public_key'),
                        $this->language->get('tab_settings'));
                }
                if (!isset($this->request->post['paycore_test_secret_key']) || !trim($this->request->post['paycore_test_secret_key'])) {
                    $this->error['warning'] = $this->error['error_test_secret_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_test_secret_key'),
                        $this->language->get('tab_settings'));
                }
            } else {
                if (!isset($this->request->post['paycore_public_key']) || !trim($this->request->post['paycore_public_key'])) {
                    $this->error['warning'] = $this->error['error_public_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_public_key'),
                        $this->language->get('tab_settings'));
                }

                if (!isset($this->request->post['paycore_secret_key']) || !trim($this->request->post['paycore_secret_key'])) {
                    $this->error['warning'] = $this->error['error_secret_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_secret_key'),
                        $this->language->get('tab_settings'));
                }

            }

        }

        return !$this->error;
    }

    protected function _setData($values) {
        $data = array();
        foreach ($values as $key => $value) {
            if (is_int($key)) {
                $data[$value] = $this->language->get($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    protected function _updateData($keys, $info = array()) {
        $data = array();
        foreach ($keys as $key) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($info[$key])) {
                $data[$key] = $info[$key];
            } else {
                $data[$key] = $this->config->get($key);
            }
        }
        return $data;
    }

    protected function validatePermission() {
        return $this->user->hasPermission('modify', 'payment/paycore');
    }

    protected function _trimData($values) {
        foreach ($values as $value) {
                if (isset($this->request->post[$value])) {
                    $this->request->post[$value] = trim($this->request->post[$value]);
                }
        }
    }

    protected function _replaceData($search, $replace, $values) {
        foreach ($values as $value) {
                if (isset($this->request->post[$value])) {
                    $this->request->post[$value] = str_replace($search, $replace, $this->request->post[$value]);
                }
        }
    }

    protected function makeUrl($route, $url = '') {
        return str_replace('&amp;', '&', $this->url->link($route, $url . '&token=' . $this->session->data['token'], 'SSL'));
    }

    protected function readLastLines($filename, $lines) {
        if (!is_file($filename)) {
            return array();
        }
        $handle = @fopen($filename, "r");
        if (!$handle) {
            return array();
        }
        $linecounter = $lines;
        $pos = -1;
        $beginning = false;
        $text = array();

        while ($linecounter > 0) {
            $t = " ";

            while ($t != "\n") {
                /* if fseek() returns -1 we need to break the cycle*/
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }

            $linecounter--;

            if ($beginning) {
                rewind($handle);
            }

            $text[$lines - $linecounter - 1] = fgets($handle);

            if ($beginning) {
                break;
            }
        }
        fclose($handle);

        return array_reverse($text);
    }
}
?>