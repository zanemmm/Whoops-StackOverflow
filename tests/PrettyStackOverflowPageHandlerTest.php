<?php
namespace Whoops;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Whoops\Handler\PrettyStackOverflowPageHandler;

class PrettyStackOverflowPageHandlerTest extends TestCase
{
    /**
     * @return Run
     */
    private function getRunInstance()
    {
        $run = new Run();
        $run->writeToOutput(false);
        $run->allowQuit(false);
        return $run;
    }

    /**
     * @return \Whoops\Handler\PrettyStackOverflowPageHandler
     */
    private function getHandler()
    {
        $handler = new PrettyStackOverflowPageHandler();
        $handler->handleUnconditionally(true);
        return $handler;
    }

    /**
     * @return RuntimeException
     */
    private function getException()
    {
        return new RuntimeException('laravel');
    }

    /**
     * @return RuntimeException
     */
    private function getNotFoundInStackOverflowException()
    {
        return new RuntimeException(
            'I guess nobody ask this questions because it is too boring, by zane'
        );
    }

    public function testHandleWithStackOverflow()
    {
        // clean cache file
        file_put_contents(__DIR__ . '/../src/Resources/caches/cache.json', '');

        // ready for test
        $run     = $this->getRunInstance();
        $handler = $this->getHandler();
        $run->pushHandler($handler);

        // test
        $response = $run->handleException($this->getException());
        $key = strpos($response, 'https://stackoverflow.com/questions/');
        $this->assertNotFalse($key);

        $response = $run->handleException($this->getNotFoundInStackOverflowException());
        $key = strpos($response, '0 results found containing');
        $this->assertNotFalse($key);
    }
}
