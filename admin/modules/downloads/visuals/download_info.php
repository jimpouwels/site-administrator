<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/visual.php";
    require_once CMS_ROOT . "view/views/information_message.php";

    class DownloadInfo extends Panel {

        private static $TEMPLATE = "downloads/download_info.tpl";
        private $_download;

        public function __construct($download) {
            parent::__construct('Bestandsinformatie');
            $this->_download = $download;
        }

        public function renderVisual(): string {
            return parent::renderVisual();
        }

        public function renderPanelContent() {
            $this->getTemplateEngine()->assign("file", $this->getFileData());
            return $this->getTemplateEngine()->fetch("modules/" . self::$TEMPLATE);
        }

        private function getFileData() {
            $file_path = UPLOAD_DIR . '/' . $this->_download->getFileName();
            $file_exists = file_exists($file_path);
            $file_data = array();
            $file_data['name'] = $this->_download->getFileName();
            if ($file_exists)
                $file_data['size'] = filesize($file_path) / 1000;
            $file_data['exists'] = $file_exists;
            return $file_data;
        }
    }
