<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/template_engine.php";
    require_once CMS_ROOT . "database/mysql_connector.php";

    class Configuration extends Panel {

        private static $CONFIGURATION_TEMPLATE = "modules/database/configuration.tpl";
        private $_template_engine;
        private $_mysql_connector;

        public function __construct() {
            parent::__construct('Database configuratie', 'configuration_panel');
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public function render(): string {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->_template_engine->assign("hostname", $this->_mysql_connector->getHostName());
            $this->_template_engine->assign("database_name", $this->_mysql_connector->getDatabaseName());
            $this->_template_engine->assign("database_type", $this->_mysql_connector->getDatabaseType());
            $this->_template_engine->assign("database_version", $this->_mysql_connector->getDatabaseVersion());
            return $this->_template_engine->fetch(self::$CONFIGURATION_TEMPLATE);
        }
    }
