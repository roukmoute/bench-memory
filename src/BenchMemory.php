<?php

namespace Roukmoute\BenchMemory;

class BenchMemory implements \Countable, \IteratorAggregate, \ArrayAccess
{
    const PEAK_MEMORY = 'Peak of memory usage';

    private $marks;
    private $width;
    private $byteFormats;

    public function __construct()
    {
        $this->marks       = [];
        $this->byteFormats = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    }

    /**
     * Get a mark.
     * If the mark does not exist, it will be automatically created.
     *
     * @param $id
     *
     * @return \Roukmoute\BenchMemory\Mark
     */
    public function __get($id)
    {
        if ($this->offsetExists($id)) {
            return $this->offsetGet($id);
        }

        $mark = new Mark($id);
        $this->offsetSet($id, $mark);

        return $mark;
    }

    /**
     * Show memory in text mode.
     *
     * @access  public
     * @return  string
     */
    public function __toString()
    {
        $out = '';
        $this->calculateWidth();
        $exponent = floor(log(memory_get_peak_usage()) / log(1024));
        $out .= $this->calculateMarks();
        $out .= sprintf('%s %s%.2f %s / %s' . "\n",
                        self::PEAK_MEMORY,
                        str_repeat(' ', $this->width - strlen(self::PEAK_MEMORY)),
                        memory_get_peak_usage() / pow(1024, $exponent),
                        $this->byteFormats[$exponent],
                        ini_get('memory_limit'));

        return $out;
    }

    private function calculateMarks()
    {
        $out = '';
        /** @var Mark $mark */
        foreach ($this as $mark) {
            $exponent = floor(log($mark->diff()) / log(1024));
            $out .= sprintf('%s %s%.2f %s' . "\n",
                            $mark,
                            str_repeat(' ', $this->width - strlen($mark)),
                            $mark->diff() / pow(1024, $exponent),
                            $this->byteFormats[$exponent]);
        }

        return $out;
    }

    private function calculateWidth()
    {
        $width = 0;
        foreach ($this->marks as $mark) {
            if (strlen($mark) > $width) {
                $width = strlen($mark);
            }
        }
        if (strlen(self::PEAK_MEMORY) > $width) {
            $width = strlen(self::PEAK_MEMORY);
        }

        $this->width = $width;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->marks);
    }

    public function offsetExists($offset)
    {
        return isset($this->marks[$offset]) || array_key_exists($offset, $this->marks);
    }

    public function offsetGet($offset)
    {
        if (isset($this->marks[$offset])) {
            return $this->marks[$offset];
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->marks[] = $value;
        }
        $this->marks[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->marks[$offset]) || array_key_exists($offset, $this->marks)) {
            unset($this->marks[$offset]);
        }
    }

    public function count()
    {
        return count($this->marks);
    }
}