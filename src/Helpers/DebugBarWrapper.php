<?php

declare(strict_types=1);

namespace App\Helpers;

use DebugBar\DebugBar;

/**
 * A simple wrapper class that allows easier access to debugbar functions.
 */
class DebugBarWrapper
{
    /**
     * @var DebugBar|null The debugbar
     */
    private $debugbar;

    public function __construct(DebugBar $debugbar = null)
    {
        $this->debugbar = $debugbar;
    }

    public function getDebugBar(): DebugBar
    {
        return $this->debugbar;
    }

    public function addCollector(\DebugBar\DataCollector\DataCollectorInterface $collector)
    {
        $this->debugbar->addCollector($collector);
    }

    public function start(string $key, string $label)
    {
        $this->debugbar['time']->startMeasure($key, $label);
    }

    public function stop(string $key)
    {
        $this->debugbar['time']->stopMeasure($key);
    }

    public function emergency($message)
    {
        $this->debugbar['messages']->emergency($message);
    }

    public function alert($message)
    {
        $this->debugbar['messages']->alert($message);
    }

    public function critical($message)
    {
        $this->debugbar['messages']->critical($message);
    }

    public function error($message)
    {
        $this->debugbar['messages']->error($message);
    }

    public function warning($message)
    {
        $this->debugbar['messages']->warning($message);
    }

    public function notice($message)
    {
        $this->debugbar['messages']->notice($message);
    }

    public function info($message)
    {
        $this->debugbar['messages']->info($message);
    }

    public function debug($message)
    {
        $this->debugbar['messages']->debug($message);
    }

    public function log($message)
    {
        $this->debugbar['messages']->log($message);
    }
}
