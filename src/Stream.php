<?php

namespace FuncMocker;

/**
 * Minimized stream wrapper implementation that is used as a way to create a function on-the-fly and include it.
 *
 * All methods are a part of the PHP stream wrapper interface.
 *
 * @internal
 */
class Stream
{
    const PROTOCOL = 'funcmocker';

    protected $content;
    protected $length;
    protected $pointer = 0;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->content = "<?php " . substr($path, strlen(self::PROTOCOL . '://'));
        $this->length = strlen($this->content);
        return true;
    }

    public function stream_read($count)
    {
        $value = substr($this->content, $this->pointer, $count);
        $this->pointer += $count;
        return $value;
    }

    public function stream_eof()
    {
        return $this->pointer >= $this->length;
    }

    public function stream_stat()
    {
        $stat = stat(__FILE__);
        $stat[7] = $stat['size'] = $this->length;
        return $stat;
    }

    public function url_stat($path, $flags)
    {
        return $this->stream_stat();
    }
}
