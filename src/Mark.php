<?php

namespace Roukmoute\BenchMemory;

class Mark
{
    protected $id;
    protected $start;
    protected $stop;
    protected $real_usage;
    protected $running;

    public function __construct($id, $real_usage = true)
    {
        $this->id         = $id;
        $this->real_usage = $real_usage;
        $this->running    = false;
    }

    public function start()
    {
        $this->start   = memory_get_usage();
        $this->running = true;
    }

    public function stop($silent = false)
    {
        if (!$this->running && !$silent) {
            throw new \Exception('Cannot stop the "' . $this->id . '" mark, because it is not running');
        }
        $this->stop    = memory_get_usage();
        $this->running = false;
    }

    public function diff()
    {
        if ($this->running) {
            $this->stop();
        }

        return $this->stop - $this->start;
    }

    public function setRealUsage($real_usage)
    {
        $this->real_usage = $real_usage;
    }

    public function getRealUsage()
    {
        return (bool)$this->real_usage;
    }

    public function isRunning()
    {
        return $this->running;
    }

    public function __toString()
    {
        return $this->id;
    }
}