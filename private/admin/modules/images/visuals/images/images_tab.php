<?php
require_once CMS_ROOT . "/modules/images/visuals/images/ImageSearch.php";
require_once CMS_ROOT . "/modules/images/visuals/images/ImageList.php";
require_once CMS_ROOT . "/modules/images/visuals/images/ImageEditor.php";

class ImagesTab extends Visual {

    private ?Image $currentImage;
    private ImageRequestHandler $requestHandler;

    public function __construct(ImageRequestHandler $requestHandler) {
        parent::__construct();
        $this->requestHandler = $requestHandler;
        $this->currentImage = $this->requestHandler->getCurrentImage();
    }

    public function getTemplateFilename(): string {
        return "modules/images/images/root.tpl";
    }

    public function load(): void {
        $this->assign("search", $this->renderImageSearch());
        if ($this->currentImage) {
            $this->assign("editor", $this->renderImageEditor());
        } else {
            $this->assign("list", $this->renderImageList());
        }
    }

    private function renderImageSearch(): string {
        return (new ImageSearch($this->requestHandler))->render();
    }

    private function renderImageList(): string {
        return (new ImageList($this->requestHandler))->render();
    }

    private function renderImageEditor(): string {
        return (new ImageEditor($this->currentImage))->render();
    }

}

?>