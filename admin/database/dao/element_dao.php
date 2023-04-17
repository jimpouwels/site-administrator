<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "database/mysql_connector.php";
    require_once CMS_ROOT . "core/model/element_type.php";
    require_once CMS_ROOT . "core/model/element.php";

    class ElementDao {

        private static string $myAllColumns = "e.id, e.follow_up, e.template_id, e.include_in_table_of_contents, t.classname, t.identifier, 
                                        t.domain_object, e.element_holder_id";
        private static ?ElementDao $instance = null;
        private MysqlConnector $_mysql_connector;

        private function __construct() {
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public static function getInstance(): ElementDao {
            if (!self::$instance) {
                self::$instance = new ElementDao();
            }
            return self::$instance;
        }

        public function getElements(ElementHolder $element_holder): array {
            $elements_info_query = "SELECT " . self::$myAllColumns . " FROM elements e, element_types t WHERE element_holder_id 
                                    = " . $element_holder->getId() . " AND t.id = e.type_id ORDER BY e.follow_up ASC";
            $result = $this->_mysql_connector->executeQuery($elements_info_query);
            $elements = array();
            while ($row = $result->fetch_assoc()) {
                $elements[] = Element::constructFromRecord($row);
            }
            return $elements;
        }

        public function getElement(int $id): ?Element {
            $query = "SELECT " . self::$myAllColumns . " FROM elements e, element_types t WHERE e.id = " . $id . " 
                      AND e.type_id = t.id;";
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                return Element::constructFromRecord($row);
            }
            return null;
        }

        public function updateElement(Element $element): void {
            $set = 'follow_up = ' . $element->getIndex();
            if (!is_null($element->getTemplateId()) && $element->getTemplateId() != '') {
                $set .= ', template_id = ' . $element->getTemplateId();
            } else {
                $set .= ', template_id = NULL';
            }
            $set .= ', include_in_table_of_contents = ' . ($element->includeInTableOfContents() ? '1' : '0');
            $query = "UPDATE elements SET " . $set . "    WHERE id = " . $element->getId();
            $this->_mysql_connector->executeQuery($query);
            $element->updateMetaData();
        }

        public function deleteElement(Element $element): void {
            $statement = $this->_mysql_connector->prepareStatement("DELETE FROM elements WHERE id = ?");
            $element_id = $element->getId();
            $statement->bind_param('i', $element_id);
            $this->_mysql_connector->executeStatement($statement);
        }

        public function getElementTypes(): array {
            $query = "SELECT * FROM element_types ORDER BY name";
            $result = $this->_mysql_connector->executeQuery($query);
            $element_types = array();
            while ($row = $result->fetch_assoc()) {
                $element_types[] = ElementType::constructFromRecord($row);
            }
            return $element_types;
        }

        public function updateElementType(ElementType $element_type): void {
            $system_default_val = 0;
            if ($element_type->getSystemDefault()) {
                $system_default_val = 1;
            }
            $query = "UPDATE element_types SET classname = '" . $element_type->getClassName() . "', icon_url = '" . $element_type->getIconUrl() . "', name = '" .
                      $element_type->getName() . "', domain_object = '" . $element_type->getDomainObject() . "', scope_id = " . 
                      $element_type->getScopeId() . ", identifier = '" . $element_type->getIdentifier() . "', system_default = " . 
                      $system_default_val . " WHERE identifier = '" . $element_type->getIdentifier() . "'";
            $this->_mysql_connector->executeQuery($query);
        }

        public function persistElementType(ElementType $element_type): void {
            $system_default_val = 0;
            if ($element_type->getSystemDefault()) {
                $system_default_val = 1;
            }
            $query = "INSERT INTO element_types (classname, icon_url, name, domain_object, scope_id, identifier, system_default)" .
                     " VALUES ('" . $element_type->getClassName() . "', '" . $element_type->getIconUrl() .
                     "', '" . $element_type->getName() . "', '" . $element_type->getDomainObject() . "', " . $element_type->getScopeId() . ", " . 
                     "'" . $element_type->getIdentifier() . "', " . $system_default_val . ")";
            $this->_mysql_connector->executeQuery($query);
            $element_type->setId($this->_mysql_connector->getInsertId());
        }

        public function deleteElementType(ElementType $element_type): void {
            $query = "DELETE FROM element_types WHERE id = " . $element_type->getId();
            $this->_mysql_connector->executeQuery($query);
        }

        public function getDefaultElementTypes(): array {
            $query = "SELECT * FROM element_types WHERE system_default = 1 ORDER BY name";
            $result = $this->_mysql_connector->executeQuery($query);
            $element_types = array();
            while ($row = $result->fetch_assoc())
                $element_types[] = ElementType::constructFromRecord($row);
            return $element_types;
        }

        public function getCustomElementTypes(): array {
            $query = "SELECT * FROM element_types WHERE system_default = 0 ORDER BY name";
            $result = $this->_mysql_connector->executeQuery($query);
            $element_types = array();
            while ($row = $result->fetch_assoc()) {
                $element_types[] = ElementType::constructFromRecord($row);
            }
            return $element_types;
        }

        public function getElementType(int $element_type_id): ?ElementType {
            $query = "SELECT * FROM element_types WHERE id = " . $element_type_id;
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                return ElementType::constructFromRecord($row);
            }
            return null;
        }

        public function getElementTypeByIdentifier(string $identifier): ?ElementType {
            $query = "SELECT * FROM element_types WHERE identifier = '$identifier'";
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                return ElementType::constructFromRecord($row);
            }
            return null;
        }

        public function getElementTypeForElement(int $element_id): ?ElementType {
            $query = "SELECT * FROM element_types t, elements e WHERE e.id = " . $element_id . " AND t.id = e.type_id";
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc()) {
                return ElementType::constructFromRecord($row);
            }
            return null;
        }

        public function createElement(ElementType $element_type, int $element_holder_id): Element {
            include_once CMS_ROOT . "elements/" . $element_type->getIdentifier() . "/" . $element_type->getDomainObject();
            $element_classname = $element_type->getClassName();
            $new_element = new $element_classname;
            $new_element->setIndex($this->getNextElementIndex($element_holder_id));
            $new_element->setScopeId($element_type->getScopeId());
            $this->persistElement($element_type, $new_element, $element_holder_id);
            return $new_element;
        }

        public function updateElementOrder(string $element_order): void {
            $element_ids = explode(',', $element_order);
            if (count($element_ids) > 0 && $element_ids[0] != '') {
                for ($counter = 0; $counter < count($element_ids); $counter += 1) {
                    $element = $this->getElement($element_ids[$counter]);
                    $element->setIndex($counter);
                    $this->updateElement($element);
                }
            }
        }

        private function persistElement(ElementType $element_type, Element $element, int $element_holder_id): void {
            $query = "INSERT INTO elements(follow_up,type_id, element_holder_id, template_id) VALUES (" . $element->getIndex() . " 
                      , " . $element_type->getId() . ", " . $element_holder_id . ", 0)";
            $this->_mysql_connector->executeQuery($query);
            $element->setId($this->_mysql_connector->getInsertId());
            $element->updateMetaData();
        }

        private function getNextElementIndex(int $element_holder_id): int {
            $query = "SELECT max(follow_up) AS next_available_index FROM elements WHERE element_holder_id = " . $element_holder_id;
            $result = $this->_mysql_connector->executeQuery($query);
            $next_index = NULL;
            while ($row = $result->fetch_assoc()) {
                $next_index = $row['next_available_index'];
                $next_index = is_null($next_index) ? 0 : ++$next_index;
            }
            return $next_index;
        }

    }
?>