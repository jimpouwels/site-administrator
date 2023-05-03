<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "database/mysql_connector.php";
    require_once CMS_ROOT . "core/model/webform.php";
    
    class WebFormDao {

        private static string $myAllColumns = "i.id, i.title";
        private static ?WebFormDao $instance = null;
        private MysqlConnector $_mysql_connector;

        private function __construct() {
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public static function getInstance(): WebFormDao {
            if (!self::$instance) {
                self::$instance = new WebFormDao();
            }
            return self::$instance;
        }

        public function getWebForm(int $form_id): ?WebForm {
            $query = "SELECT " . self::$myAllColumns . " FROM webforms i WHERE id = " . $form_id;
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                return WebForm::constructFromRecord($row);
            }
            return null;
        }

        public function getAllWebForms(): array {
            $webforms = array();
            $query = "SELECT " . self::$myAllColumns . " FROM webforms i";
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                $webforms[] = WebForm::constructFromRecord($row);
            }
            return $webforms;
        }

        public function persistWebForm($webform): void {
            $query = "INSERT INTO webforms (title) "
                     . "VALUES ('" . $webform->getTitle() . "')";
            $this->_mysql_connector->executeQuery($query);
            $webform->setId($this->_mysql_connector->getInsertId());
        }

        public function updateWebForm($webform): void {
            $query = "UPDATE webforms SET title = ? WHERE id = ?";
            $statement = $this->_mysql_connector->prepareStatement($query);
            $id = $webform->getId();
            $title = $webform->getTitle();
            $statement->bind_param("si", $title, $id);
            $this->_mysql_connector->executeStatement($statement);
        }
    }