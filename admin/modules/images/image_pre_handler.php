<?php    
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "database/dao/image_dao.php";
    require_once CMS_ROOT . "request_handlers/module_request_handler.php";
    require_once CMS_ROOT . "utilities/file_utility.php";
    require_once CMS_ROOT . "modules/images/image_form.php";
    
    class ImagePreHandler extends ModuleRequestHandler {

        private static $IMAGE_QUERYSTRING_KEY = "image";
        private static $TITLE_SEARCH_QUERYSTRING_KEY = "s_title";
        private static $FILENAME_SEARCH_QUERYSTRING_KEY = "s_filename";
        private static $LABEL_SEARCH_QUERYSTRING_KEY = "s_label";
    
        private $_image_dao;
        private $_current_image;
    
        public function __construct() {
            $this->_image_dao = ImageDao::getInstance();
        }
    
        public function handleGet() {
            $this->_current_image = $this->getImageFromGetRequest();
        }
        
        public function handlePost() {
            $this->_current_image = $this->getImageFromPostRequest();
            if ($this->isUpdateImageAction())
                $this->updateImage();
            else if ($this->isDeleteImageAction())
                $this->deleteImage();
            else if ($this->isAddImageAction())
                $this->addImage();
        }
        
        public function getCurrentImage() {
            return $this->_current_image;
        }
        
        public function getCurrentSearchTitleFromGetRequest() {
            return $this->getQueryStringValueFromGetRequest(self::$TITLE_SEARCH_QUERYSTRING_KEY);
        }
        
        public function getCurrentSearchFilenameFromGetRequest() {
            return $this->getQueryStringValueFromGetRequest(self::$FILENAME_SEARCH_QUERYSTRING_KEY);
        }
        
        public function getCurrentSearchLabelFromGetRequest() {
            return $this->getQueryStringValueFromGetRequest(self::$LABEL_SEARCH_QUERYSTRING_KEY);
        }
        
        private function updateImage() {
            $image_form = new ImageForm($this->_current_image);
            try {
                $image_form->loadFields();
                $this->addNewlySelectedLabelsToImage($image_form->getSelectedLabels());
                $this->deleteSelectedLabelsFromImage();
                $this->saveUploadedImage();
                $this->_image_dao->updateImage($this->_current_image);
                $this->sendSuccessMessage("Afbeelding succesvol opgeslagen");
            } catch (FormException $e) {
                $this->sendErrorMessage("Afbeelding niet opgeslagen, verwerk de fouten");
            }
        }

        private function deleteImage() {
            $this->_image_dao->deleteImage($this->_current_image);
            $this->sendSuccessMessage("Afbeelding succesvol verwijderd");
            $this->redirectTo("/admin/index.php");
        }

        private function addImage() {
            $new_image = $this->_image_dao->createImage();
            $this->sendSuccessMessage("Afbeelding succesvol aangemaakt");
            $this->redirectTo("/admin/index.php?image=" . $new_image->getId());
        }
        
        private function getImageFromPostRequest() {
            $image = null;
            $image_id = $this->getImageIdFromPostRequest();
            if (!is_null($image_id))
                $image = $this->_image_dao->getImage($image_id);
            return $image;
        }
        
        private function getImageFromGetRequest() {
            $current_image = null;
            if (isset($_GET[self::$IMAGE_QUERYSTRING_KEY]) && $_GET[self::$IMAGE_QUERYSTRING_KEY] != "")
                $current_image = $this->_image_dao->getImage($_GET[self::$IMAGE_QUERYSTRING_KEY]);
            return $current_image;
        }
        
        private function addNewlySelectedLabelsToImage($selected_labels) {
            if (count($selected_labels) == 0) {
                return;
            }
            $existing_labels = $this->_image_dao->getLabelsForImage($this->_current_image->getId());
            foreach ($selected_labels as $selected_label_id) {
                if (!$this->isLabelAlreadyAdded($selected_label_id, $existing_labels)) {
                    $this->_image_dao->addLabelToImage($selected_label_id, $this->_current_image);
                }
            }
        }
        
        private function isLabelAlreadyAdded($selected_label_id, $existing_labels) {
            foreach ($existing_labels as $existing_label) {
                if ($selected_label_id == $existing_label->getId()) {
                    return true;
                }
            }
            return false;
        }
        
        private function deleteSelectedLabelsFromImage() {
            $image_labels = $this->_image_dao->getLabelsForImage($this->_current_image->getId());
            foreach ($image_labels as $image_label)
                if (isset($_POST["label_" . $image_label->getId() . "_delete"]))
                    $this->_image_dao->deleteLabelForImage($image_label->getId(), $this->_current_image);
        }

        private function saveUploadedImage() {
            $new_file_name = $this->getNewImageFilename();
            if (is_uploaded_file($_FILES["image_file"]["tmp_name"])) {
                $this->deletePreviousImage();
                $this->moveImageToUploadDirectory($new_file_name);
                $this->saveThumbnailForUploadedImage($new_file_name);
            }
        }
        
        private function getNewImageFilename() {
            $current_image_id = $this->_current_image->getId();
            $uploaded_image_filename = $_FILES["image_file"]["name"];
            return "UPLIMG-00$current_image_id" . "_$uploaded_image_filename";
        }
        
        private function saveThumbnailForUploadedImage($new_file_name) {
            $thumb_file_name = "THUMB-" . $new_file_name;
            FileUtility::saveThumb($new_file_name, UPLOAD_DIR, $thumb_file_name, 50, 50);
            $this->_current_image->setThumbFileName($thumb_file_name);
        }
        
        private function moveImageToUploadDirectory($new_file_name) {
            rename($_FILES["image_file"]["tmp_name"], UPLOAD_DIR . "/" . $new_file_name);
            $this->_current_image->setFileName($new_file_name);    
        }
        
        private function deletePreviousImage() {
            FileUtility::deleteImage($this->_current_image, UPLOAD_DIR);
        }
        
        private function getImageIdFromPostRequest() {
            $image_id = null;
            if (isset($_POST["image_id"]) && $_POST["image_id"])
                $image_id = $_POST["image_id"];
            return $image_id;
        }
        
        private function isAddImageAction() {
            return isset($_POST["add_image_action"]) && $_POST["add_image_action"] == "add_image";
        }
        
        private function isDeleteImageAction() {
            return isset($_POST['action']) && $_POST["action"] == "delete_image" && isset($_POST["image_id"]);
        }
        
        private function isUpdateImageAction() {
            return isset($_POST['action']) && $_POST["action"] == "update_image" && isset($_POST["image_id"]);
        }
        
        private function getQueryStringValueFromGetRequest($query_string_key) {
            $value = null;
            if (isset($_GET[$query_string_key]) && $_GET[$query_string_key] != '')
                $value = $_GET[$query_string_key];
            return $value;
        }
        
    }
    
?>