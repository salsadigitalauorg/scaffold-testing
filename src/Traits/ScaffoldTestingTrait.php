<?php

declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Traits;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;
use Drupal\group\Entity\Group;
use Drupal\user\Entity\User;

/**
 * Provides common step definitions for Behat tests.
 */
trait ScaffoldTestingTrait
{
    protected Process $process;
    protected ?User $user = null;

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->process = new Process(['php', '-S', 'localhost:8888']);
        $this->process->start();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(AfterScenarioScope $scope): void
    {
        if (isset($this->process)) {
            $this->process->stop();
        }
    }

    /**
     * @And I belong to group :groupName
     */
    public function iBelongToGroup($groupName): void
    {
        if (!$this->user) {
            throw new \Exception("No user is currently logged in. Please log in first.");
        }

        $groups = \Drupal::entityTypeManager()
            ->getStorage('group')
            ->loadByProperties(['label' => $groupName]);
        $group = reset($groups);

        if ($group) {
            $group->addMember($this->user);
            $group->save();
        } else {
            throw new \Exception("Group '$groupName' not found");
        }
    }

    /**
     * @When I go to the forum index page
     */
    public function iGoToTheForumIndexPage(): void
    {
        $this->getSession()->visit($this->locatePath('/portal/community-forum'));
    }

    /**
     * @When I go to a specific forum page
     */
    public function iGoToASpecificForumPage(): void
    {
        $this->getSession()->visit($this->locatePath('/node/358'));
    }

    /**
     * @Then I should be able to access the page
     */
    public function iShouldBeAbleToAccessThePage(): void
    {
        $statusCode = $this->getSession()->getStatusCode();
        if ($statusCode !== 200) {
            $pageContent = $this->getSession()->getPage()->getContent();
            throw new \Exception("Expected to access the page, but got status code: $statusCode. Page content: " . substr($pageContent, 0, 500));
        }
    }

    /**
     * @Then I should be denied access
     */
    public function iShouldBeDeniedAccess(): void
    {
        $statusCode = $this->getSession()->getStatusCode();
        if ($statusCode !== 403) {
            $pageContent = $this->getSession()->getPage()->getContent();
            throw new \Exception("Expected to be denied access (403), but got status code: $statusCode. Page content: " . substr($pageContent, 0, 500));
        }
    }

    /**
     * @Then I print all form fields
     */
    public function iPrintAllFormFields(): void
    {
        $page = $this->getSession()->getPage();
        $fields = $page->findAll('css', 'input, select, textarea');
        foreach ($fields as $field) {
            echo "Field: " . $field->getAttribute('name') . " | " . $field->getAttribute('id') . "\n";
        }
    }

    // Add your step definitions here...
} 