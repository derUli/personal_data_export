<?php

use GDPR\PersonalData\Query;

class PersonalDataController extends MainClass
{
    const MODULE_NAME = "personal_data_export";

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "list.php");
    }

    public function getSettingsHeadline()
    {
        return get_translation("personal_data");
    }

    public function exportData()
    {
        $term = Request::getVar("query", null, "str");
        $html = $this->_exportData($term);
        $escapedTerm = str_replace("@", "_at_", trim($term));
        $fileName = "data_export-" . date('Y-m-d_h-i') . "-" . $escapedTerm . ".html";

        if (!$html) {
            ExceptionResult(get_translation("not_found"));
        }

        DownloadResultFromString($html, $fileName);
    }

    public function _exportData(?string $term): ?string
    {
        if ($term) {
            $query = new Query();
            ViewBag::set("person", $query->getData($term));

            return Template::executeModuleTemplate(self::MODULE_NAME, "export.php");
        }
        return null;
    }

    public function deleteData()
    {
        $term = Request::getVar("query", null, "str");
        $this->_deleteData($term);

        if (!$this->_deleteData($term)) {
            ExceptionResult(get_translation("cant_delete_current_user"), HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        return ActionResult("personal_data_delete_success", $term);
    }

    public function _deleteData(?string $term): bool
    {
        $user = User::fromSessionData();
        if (!$term) {
            return false;
        }
        
        if ($user && $user->getEmail() == $term) {
            return false;
        }

        $query = new Query();
        $query->deleteData($term);
        return true;
    }
}
