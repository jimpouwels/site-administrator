<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "core/model/entity.php";
    
    class User extends Entity {
    
        private string $_username;
        private string $_email_address;
        private string $_first_name;
        private string $_prefix;
        private string $_last_name;
        private string $_password;
        private string $_uuid;
        
        public function getUsername(): string {
            return $this->_username;
        }
        
        public function setUsername(string $username): void {
            $this->_username = $username;
        }
        
        public function getPassword(): string {
            return $this->_password;
        }
        
        public function setPassword(string $password): void {
            $this->_password = $password;
        }
        
        public function getEmailAddress(): string {
            return $this->_email_address;
        }
        
        public function setEmailAddress(string $email_address): void {
            $this->_email_address = $email_address;
        }
        
        public function getFirstName(): string {
            return $this->_first_name;
        }
        
        public function setFirstName(string $first_name): void {
            $this->_first_name = $first_name;
        }
        
        public function getLastName(): string {
            return $this->_last_name;
        }
        
        public function setLastName(string $last_name): void {
            $this->_last_name = $last_name;
        }
        
        public function getPrefix(): string {
            return $this->_prefix;
        }
        
        public function setPrefix(string $prefix): void {
            $this->_prefix = $prefix;
        }
        
        public function getUuid(): string {
            return $this->_uuid;
        }
        
        public function setUuid(string $uuid): void {
            $this->_uuid = $uuid;
        }
        
        public function getFullName(): string {
            $full_name = $this->getFirstName();
            if (!is_null($this->getPrefix()) && $this->getPrefix() != '') {
                $full_name = $full_name . ' ' . $this->getPrefix();
            }
            $full_name = $full_name . ' ' . $this->getLastName();
            return $full_name;
        }
        
        public function isLoggedInUser(): bool {
            return $this->getUsername() == $_SESSION["username"];
        }
        
        public static function constructFromRecord(array $record): User {
            $user = new User();
            $user->setId($record['id']);
            $user->setUsername($record['username']);
            $user->setEmailAddress($record['email_address']);
            $user->setFirstName($record['first_name']);
            $user->setLastName($record['last_name']);
            $user->setPrefix($record['prefix']);
            $user->setUuid($record['uuid']);
            return $user;
        }
    }
    
    
?>