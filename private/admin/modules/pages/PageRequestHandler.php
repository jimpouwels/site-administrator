<?php
require_once CMS_ROOT . "/authentication/Authenticator.php";
require_once CMS_ROOT . "/modules/pages/service/PageInteractor.php";
require_once CMS_ROOT . "/modules/blocks/service/BlockInteractor.php";
require_once CMS_ROOT . "/modules/pages/PageForm.php";
require_once CMS_ROOT . "/database/dao/ElementDaoMysql.php";
require_once CMS_ROOT . "/request_handlers/ElementHolderRequestHandler.php";
require_once CMS_ROOT . "/request_handlers/exceptions/ElementHolderContainsErrorsException.php";
require_once CMS_ROOT . '/friendly_urls/FriendlyUrlManager.php';

class PageRequestHandler extends ElementHolderRequestHandler {

    private static string $PAGE_ID_POST = "element_holder_id";
    private static string $PAGE_ID_GET = "page";
    private static int $FALLBACK_PAGE_ID = 1;

    private Page $currentPage;
    private PageService $pageService;
    private BlockService $blockService;
    private FriendlyUrlManager $friendlyUrlManager;

    public function __construct() {
        parent::__construct();
        $this->friendlyUrlManager = FriendlyUrlManager::getInstance();
        $this->pageService = PageInteractor::getInstance();
        $this->blockService = BlockInteractor::getInstance();
    }

    public function handleGet(): void {
        $this->currentPage = $this->getPageFromGetRequest();
    }

    public function handlePost(): void {
        try {
            parent::handlePost();
            if ($this->isUpdatePageAction()) {
                $this->updatePage();
            } else if ($this->isDeletePageAction()) {
                $this->deletePage();
            } else if ($this->isAddSubPageAction()) {
                $this->addSubPage();
            } else if ($this->isMoveUpAction()) {
                $this->pageService->moveUp($this->currentPage);
            } else if ($this->isMoveDownAction()) {
                $this->pageService->moveDown($this->currentPage);
            }
        } catch (ElementHolderContainsErrorsException) {
            $this->sendErrorMessage($this->getTextResource('page_not_saved_error_message'));
        }
    }

    public function loadElementHolderFromPostRequest(): ?ElementHolder {
        $this->currentPage = $this->getPageFromPostRequest();
        return $this->currentPage;
    }

    public function getCurrentPage(): ?Page {
        return $this->currentPage;
    }

    private function updatePage(): void {
        try {
            $pageForm = new PageForm($this->currentPage);
            $pageForm->loadFields();
            $this->pageService->addSelectedBlocks($this->currentPage, $pageForm->getSelectedBlocks());
            $this->deleteSelectedBlocksFromPage();
            $this->pageService->updatePage($this->currentPage);
            $this->updateElementHolder($this->currentPage);
            $this->friendlyUrlManager->insertOrUpdateFriendlyUrlForPage($this->currentPage);
            $this->sendSuccessMessage($this->getTextResource('page_saved_message'));
        } catch (FormException) {
            $this->sendErrorMessage($this->getTextResource('page_not_saved_error_message'));
        }
    }

    private function deleteSelectedBlocksFromPage(): void {
        $currentPageBlocks = $this->blockService->getBlocksByPage($this->currentPage);
        foreach ($currentPageBlocks as $currentPageBlock) {
            if ($this->isBlockSelectedForDeletion($currentPageBlock)) {
                $this->blockService->deleteBlockFromPage($currentPageBlock->getId(), $this->currentPage);
            }
        }
    }

    private function isBlockSelectedForDeletion(Block $currentPageBlock): bool {
        return isset($_POST["block_" . $this->currentPage->getId() . "_" . $currentPageBlock->getId() . "_delete"]);
    }

    private function deletePage(): void {
        $this->pageService->deletePage($this->currentPage);
        $this->sendSuccessMessage($this->getTextResource('page_deleted_message'));
        $this->redirectTo($this->getBackendBaseUrl() . "&page=1");
    }

    private function addSubPage(): void {
        $newPage = $this->pageService->addSubPageTo($this->currentPage);
        $this->sendSuccessMessage($this->getTextResource('page_added_message'));
        $this->redirectTo($this->getBackendBaseUrl() . "&page=" . $newPage->getId());
    }

    private function getPageFromPostRequest(): Page {
        return $this->pageService->getPageById($_POST[self::$PAGE_ID_POST]);
    }

    private function getPageFromGetRequest(): Page {
        if (isset($_GET[self::$PAGE_ID_GET])) {
            return $this->pageService->getPageById($_GET[self::$PAGE_ID_GET]);
        } else {
            return $this->pageService->getPageById(self::$FALLBACK_PAGE_ID);
        }
    }

    private function isUpdatePageAction(): bool {
        return isset($_POST["action"]) && $_POST["action"] == "update_element_holder";
    }

    private function isDeletePageAction(): bool {
        return isset($_POST["action"]) && $_POST["action"] == "delete_page";
    }

    private function isAddSubPageAction(): bool {
        return isset($_POST["action"]) && $_POST["action"] == "sub_page";
    }

    private function isMoveUpAction(): bool {
        return isset($_POST["action"]) && $_POST["action"] == "move_up";
    }

    private function isMoveDownAction(): bool {
        return isset($_POST["action"]) && $_POST["action"] == "move_down";
    }

}

?>
