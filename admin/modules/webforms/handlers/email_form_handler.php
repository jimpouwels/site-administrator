<?php
    defined('_ACCESS') or die;
  
    require_once CMS_ROOT . 'modules/webforms/handlers/form_handler.php';
    require_once CMS_ROOT . 'modules/webforms/handlers/handler_property.php';
    require_once CMS_ROOT . 'core/model/webform.php';
    require_once CMS_ROOT . 'database/dao/settings_dao.php';

    class EmailFormHandler extends Formhandler {

        public static string $TYPE = 'email_form_handler';
        private SettingsDao $_settings_dao;

        public function __construct() {
            $this->_settings_dao = SettingsDao::getInstance();
        }

        public function getRequiredProperties(): array {
            return array(
                new HandlerProperty('target_email_address', 'textfield'),
                new HandlerProperty('subject', 'textfield'),
                new HandlerProperty('template', 'textarea')
            );
        }

        public function getNameResourceIdentifier(): string {
            return 'webforms_email_form_handler_name';
        }

        public function getType(): string {
            return self::$TYPE;
        }

        public function handle(array $fields): void {
            $message = $this->getFilledInPropertyValue('template');
            $subject = $this->getFilledInPropertyValue('subject');
            $target_email_address = $this->_settings_dao->getSettings()->getEmailAddress();
            if (!$target_email_address) {
                $target_email_address = $this->getProperty('target_email_address');
            }
            $headers = array(
                'From' => $target_email_address
            );
            mail($target_email_address, $subject, $message, $headers);
        }
    }
?>