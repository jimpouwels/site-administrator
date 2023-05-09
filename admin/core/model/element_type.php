<?php
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "core/model/presentable.php";
    require_once CMS_ROOT . "database/dao/scope_dao.php";

    class ElementType extends Entity {
    
        private string $_class_name;
        private string $_domain_object;
        private string $_icon_url;
        private string $_identifier;
        private int $_scope_id;
        private bool $_system_default;
        
        public function getClassName(): string {
            return $this->_class_name;
        }
        
        public function setClassName(string $class_name): void {
            $this->_class_name = $class_name;
        }
        
        public function getRootDirectory(): string {
            return  "elements/" . $this->_identifier;
        }
        
        public function getIconUrl(): string {
            return $this->_icon_url;
        }
        
        public function setIconUrl(string $icon_url): void {
            $this->_icon_url = $icon_url;
        }
        
        public function getIdentifier(): string {
            return $this->_identifier;
        }
        
        public function setIdentifier(string $identifier): void {
            $this->_identifier = $identifier;
        }
        
        public function getDomainObject(): string {
            return $this->_domain_object;
        }
        
        public function setDomainObject(string $domain_object): void {
            $this->_domain_object = $domain_object;
        }
        
        public function getScope(): Scope {
            $dao = ScopeDao::getInstance();
            return $dao->getScope($this->_scope_id);
        }
        
        public function getScopeId() {
            return $this->_scope_id;
        }
        
        public function setScopeId($scope_id) {
            $this->_scope_id = $scope_id;
        }
        
        public function getSystemDefault() {
            return $this->_system_default;
        }
        
        public function setSystemDefault($system_default) {
            $this->_system_default = $system_default;
        }
        
        public static function constructFromRecord($row) {
            $element_type = new ElementType();
            $element_type->initFromDb($row);
            return $element_type;
        }
        
        protected function initFromDb(array $row): void {
            $this->setClassName($row['classname']);
            $this->setIconUrl($row['icon_url']);
            $this->setIdentifier($row['identifier']);
            $this->setDomainObject($row['domain_object']);
            $this->setScopeId($row['scope_id']);
            $this->setSystemDefault($row['system_default']);
            parent::initFromDb($row);
        }
    
    }
    
?>