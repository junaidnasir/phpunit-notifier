<?php

namespace PHPUnitNotifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Joli\JoliNotif\Util\OsHelper;

use \PHPUnit\Framework\Test as Test;
use \PHPUnit\Framework\Warning as Warning;
use \PHPUnit\Framework\TestSuite as TestSuite;
use \PHPUnit\Framework\AssertionFailedError as AssertionFailedError;

class NotifierListener extends \PHPUnit\Framework\TestCase implements \PHPUnit\Framework\TestListener
{
    private $notifier;
    private $errors = 0;
    private $failures = 0;
    private $tests = 0;
    private $suites = 0;
    private $ended_suites = 0;
    private static $is_darwin;

    public function __construct()
    {
        $this->notifier = NotifierFactory::create();
        if (self::$is_darwin === null) {
            self::$is_darwin = OsHelper::isMacOS();
        }
    }

    public function addError(Test $test, \Exception $e, $time)
    {
        $this->errors++;
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->failures++;
    }

    public function addWarning(Test $test, Warning $e, $time)
    {

    }

    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {

    }
    public function addRiskyTest(Test $test, \Exception $e, $time)
    {

    }
    public function addSkippedTest(Test $test, \Exception $e, $time)
    {

    }
    public function startTestSuite(TestSuite $suite)
    {
        $this->suites++;
    }

    public function endTestSuite(TestSuite $suite)
    {
        $this->ended_suites++;

        if ($this->suites > $this->ended_suites) {
            return;
        }

        $failures = $this->errors + $this->failures;
        if ($failures === 0) {
            $title = sprintf('%sSuccess', self::$is_darwin ? '✅ ' : '');
            $body  = sprintf('%d/%d tests passed', $this->tests, $this->tests);
            $icon  = '/Users/junaidnasir/Drive/icons/cancel.png';
        } else {
            $title = sprintf('%sFailed', self::$is_darwin ? '🚫 ' : '');
            $body  = sprintf('%d/%d tests failed', $failures, $this->tests);
            $icon  = '/Users/junaidnasir/Drive/icons/check.png';
        }

        $notification = (new Notification())
            ->setTitle($title)
            ->setBody($body)
            ->setIcon($icon)
            ->addOption('subtitle', 'This is a subtitle')
        ;
        $this->notifier->send($notification);
    }

    public function startTest(Test $test)
    {
        $this->tests++;
    }
    public function endTest(Test $test, $time)
    {

    }
}
