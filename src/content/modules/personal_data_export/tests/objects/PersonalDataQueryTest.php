<?php

use GDPR\PersonalData\CorePersonalDataResponder;

class PersonalDataQueryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
        Translation::loadAllModuleLanguageFiles("en");
    }

    protected function tearDown(): void
    {
        Database::deleteFrom("users", "username like 'testuser%'");
    }

    public function getTestUser(): User
    {
        $user = new User();
        $user->setUsername("testuser2");
        $user->setEmail("foo123@bar.de");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        $user->save();
        return $user;
    }

    public function testSearchPerson()
    {
        $this->getTestUser();
        $query = new CorePersonalDataResponder();

        $this->assertEquals(
            "foo123@bar.de",
            $query->searchPerson("testuser2")[0]->email
        );

        $this->assertEquals(
            "foo123@bar.de",
            $query->searchPerson("foo123@bar.de")[0]->email
        );

        $this->assertEquals(
            "foo123@bar.de",
            $query->searchPerson("Doe")[0]->email
        );


        $this->assertEquals(
            "foo123@bar.de",
            $query->searchPerson("Doe, John")[0]->email
        );

        $this->assertNull($query->searchPerson("Existiert, Nicht")[0]->email);
    }
}
