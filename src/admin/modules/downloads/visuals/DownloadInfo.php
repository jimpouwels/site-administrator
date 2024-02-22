<?php

namespace Obcato\Core\admin\modules\downloads\visuals;

use Obcato\Core\admin\modules\downloads\model\Download;
use Obcato\Core\admin\view\TemplateData;
use Obcato\Core\admin\view\views\Panel;
use const Obcato\Core\admin\UPLOAD_DIR;

class DownloadInfo extends Panel {

    private Download $download;

    public function __construct(Download $download) {
        parent::__construct('Bestandsinformatie');
        $this->download = $download;
    }

    public function getPanelContentTemplate(): string {
        return "modules/downloads/download_info.tpl";
    }

    public function loadPanelContent(TemplateData $data): void {
        $data->assign("file", $this->getFileData());
    }

    private function getFileData(): array {
        $filePath = UPLOAD_DIR . '/' . $this->download->getFilename();
        $fileExists = file_exists($filePath);
        $fileData = array();
        $fileData['name'] = $this->download->getFilename();
        if ($fileExists) {
            $fileData['size'] = filesize($filePath) / 1000;
        }
        $fileData['exists'] = $fileExists;
        return $fileData;
    }
}
