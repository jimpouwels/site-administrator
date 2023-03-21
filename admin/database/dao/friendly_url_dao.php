<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "database/exception/dao_exception.php";

    class FriendlyUrlDao {

        private static $instance;
        private $_mysql_connector;

        private function __construct() {
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public static function getInstance(): FriendlyUrlDao {
            if (!self::$instance) {
                self::$instance = new FriendlyUrlDao();
            }
            return self::$instance;
        }

        public function insertFriendlyUrl($url, $element_holder): void {
            $query = "INSERT INTO friendly_urls (url, element_holder_id) VALUES (?, ?)";
            $statement = $this->_mysql_connector->prepareStatement($query);

            $element_holder_id = $element_holder->getId();
            $statement->bind_param("si", $url, $element_holder_id);
            $this->_mysql_connector->executeStatement($statement);
        }

        public function updateFriendlyUrl($url, $page): void {
            $query = "UPDATE friendly_urls SET url = ? WHERE element_holder_id = ?";
            $statement = $this->_mysql_connector->prepareStatement($query);

            $element_holder_id = $page->getId();
            $statement->bind_param("si", $url, $element_holder_id);
            $this->_mysql_connector->executeStatement($statement);
        }

        public function getUrlFromElementHolder($element_holder): string {
            $query = "SELECT url FROM friendly_urls WHERE element_holder_id = ?";
            $statement = $this->_mysql_connector->prepareStatement($query);
            $element_holder_id = $element_holder->getId();
            $statement->bind_param("i", $element_holder_id);
            $result = $this->_mysql_connector->executeStatement($statement);
            while ($row = $result->fetch_assoc()) {
                return $row['url'];
            }
            throw new DaoException(sprintf("URL not found for element holder %s", $element_holder->getId()));
        }

        public function getElementHolderIdFromUrl($url): string {
            $query = "SELECT element_holder_id FROM friendly_urls WHERE url = ?";
            $statement = $this->_mysql_connector->prepareStatement($query);
            $statement->bind_param("s", $url);
            $result = $this->_mysql_connector->executeStatement($statement);
            while ($row = $result->fetch_assoc()) {
                return $row['element_holder_id'];
            }
            throw new DaoException(sprintf("ElementHolderId could not be found from URL %s", $url));
        }
    }
