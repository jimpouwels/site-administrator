<?php	// No direct access	defined('_ACCESS') or die;		require_once "dao/image_dao.php";	require_once "core/http/http_request_handler.php";		class LabelPreHandler extends HttpRequestHandler {		private static $LABEL_QUERYSTRING_KEY = "label";			private $_image_dao;			public function __construct() {			$this->_image_dao = ImageDao::getInstance();		}			public function handleGet() {		}				public function handlePost() {			if ($this->isUpdateLabelAction()) {				$this->updateLabel();			}			if ($this->isDeleteLabelsAction()) {				$this->deleteLabels();			}		}		public function getCurrentLabelFromGetRequest() {			$current_label = null;			if (isset($_GET[self::$LABEL_QUERYSTRING_KEY])) {				$label_id = $_GET[self::$LABEL_QUERYSTRING_KEY];				$current_label = $this->_image_dao->getImageLabel($label_id);			}			return $current_label;		}				private function isUpdateLabelAction() {			return isset($_POST["action"]) && $_POST["action"] == "update_label";		}				private function isDeleteLabelsAction() {			return isset($_POST['label_delete_action']) && $_POST['label_delete_action'] == 'delete_labels';		}				private function updateLabel() {			global $errors;			$name = FormValidator::checkEmpty("name", "Titel is verplicht");						if ($name != "") {				$existing_label = $this->_image_dao->getLabelByName($name);								if (!is_null($existing_label) && !(isset($_GET["label"]) && $_GET["label"] == $existing_label->getId())) {					$errors["name_error"] = "Er bestaat al een label met deze naam";				}			}						if (count($errors) == 0) {				if (isset($_POST["label_id"]) && $_POST["label_id"] != "") {					$current_label = $this->_image_dao->getLabel($_POST["label_id"]);					$current_label->setName($name);					$this->_image_dao->updateLabel($current_label);					Notifications::setSuccessMessage("Label succesvol opgeslagen");				} else if (isset($_GET["new_label"])) {					// create new label					$new_label = $this->_image_dao->createLabel();					$new_label->setName($name);					$this->_image_dao->updateLabel($new_label);					Notifications::setSuccessMessage("Label succesvol aangemaakt");					header("Location: /admin/index.php?label=" . $new_label->getId());					exit();				}			} else {				Notifications::setFailedMessage("Label niet opgeslagen, verwerk de fouten");			}		}				private function deleteLabels() {		$labels = $this->_image_dao->getAllLabels();		foreach ($labels as $label) {			if (isset($_POST["label_" . $label->getId() . "_delete"])) {				$this->_image_dao->deleteLabel($label);			}		}		Notifications::setSuccessMessage("Label(s) succesvol verwijderd");	}			}	?>