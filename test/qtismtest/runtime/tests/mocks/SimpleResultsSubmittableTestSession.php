<?php

namespace qtismtest\runtime\tests\mocks;

use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentTestSession;

class SimpleResultsSubmittableTestSession extends AssessmentTestSession
{
    private $submittedTestResults = [];

    private $submittedItemResults = [];

    protected function submitTestResults()
    {
        foreach ($this as $id => $var) {
            $this->addTestResult($id, $var->getValue());
        }
    }

    protected function submitItemResults(AssessmentItemSession $assessmentItemSession, $occurence = 0)
    {
        foreach ($assessmentItemSession as $id => $var) {
            $this->addItemResultResult($assessmentItemSession->getAssessmentItem()->getIdentifier() . '.' . $occurence . '.' . $id, $var->getValue());
        }
    }

    protected function addTestResult($identifier, $value)
    {
        if (isset($this->submittedTestResults[$identifier]) === false) {
            $this->submittedTestResults[$identifier] = [];
        }

        $this->submittedTestResults[$identifier][] = $value;
    }

    protected function addItemResultResult($identifier, $value)
    {
        if (isset($this->submittedItemResults[$identifier]) === false) {
            $this->submittedItemResults[$identifier] = [];
        }

        $this->submittedItemResults[$identifier][] = $value;
    }

    public function getSubmittedTestResults()
    {
        return $this->submittedTestResults;
    }

    public function getSubmittedItemResults()
    {
        return $this->submittedItemResults;
    }
}
