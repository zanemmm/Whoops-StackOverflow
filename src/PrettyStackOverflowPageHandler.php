<?php
/**
 * Created by PhpStorm.
 * @author zane <https://www.github.com/zanemmm>
 * @license MIT
 * Date: 18-8-31
 */
namespace Whoops\Handler;

use Whoops\Exception\Inspector;
use Whoops\RunInterface;

class PrettyStackOverflowPageHandler extends Handler
{
    protected $handler;

    public function __construct()
    {
        $this->handler = new PrettyPageHandler();
    }

    public function handle()
    {
        $this->handler->addResourcePath(__DIR__ . '/Resources');
        return $this->handler->handle();
    }

    public function setRun(RunInterface $run)
    {
        $this->handler->setRun($run);
    }

    public function setException($exception)
    {
        $this->handler->setException($exception);
    }

    public function setInspector(Inspector $inspector)
    {
        $this->handler->setInspector($inspector);
    }

    public function __call($method, $arguments)
    {
        $this->handler->$method(...$arguments);
    }
}
