<?php

use Spatie\Snapshots\MatchesSnapshots;

class PersonalDataControllerTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
        Translation::loadAllModuleLanguageFiles("en");
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        Database::deleteFrom("users", "username like 'testuser%'");
    }

    public function testGetSettingsHeadline()
    {
        $controller = new PersonalDataController();
        $this->assertMatchesTextSnapshot($controller->getSettingsHeadline());
    }

    public function testSettings()
    {
        $controller = new PersonalDataController();
        $this->assertMatchesHtmlSnapshot($controller->settings());
    }

    public function testExportDataReturnsString()
    {
        $user = $this->getTestUser();

        $controller = new PersonalDataController();
        $html = $controller->_exportData($user->getEmail());

        $this->assertMatchesHtmlSnapshot($html);
    }

    public function testExportDataReturnsNull()
    {
        $controller = new PersonalDataController();
        $html = $controller->_exportData(null);

        $this->assertNull($html);
    }

    public function getTestUser(): User
    {
        $user = new User();
        $user->setUsername("testuser1");
        $user->setEmail("foo@bar.de");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        $user->save();

        return $user;
    }

    public function testDeleteDataReturnsTrue()
    {
        $user = $this->getTestUser();
        $controller = new PersonalDataController();
        $html = $controller->_deleteData($user->getEmail());

        $this->assertTrue($html);
    }

    public function testDeleteDataReturnsFalse()
    {
        $controller = new PersonalDataController();
        $this->assertFalse($controller->_deleteData(null));

        $user = $this->getTestUser();

        $_SESSION = [
            "login_id" => $user->getId()
        ];
        
        $this->assertFalse(
            $controller->_deleteData($user->getEmail())
        );
    }
}
