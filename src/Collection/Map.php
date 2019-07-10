<?php

namespace WecarSwoole\Collection;

use WecarSwoole\Exceptions\Exception;

/**
 * Class Map
 * @package WecarSwoole\Collection
 */
class Map implements \Iterator, \ArrayAccess, \Countable
{
    private $values = [];
    private $keys = [];
    private $count = 0;
    private $current = 0;
    private $class;

    /**
     * @param array $array
     * @param string $class 类型限制
     * @throws Exception
     */
    public function __construct(array $array = [], string $class = null)
    {
        if ($class) {
            // 有类型限制，检查类型
            foreach ($array as $item) {
                if (!is_a($item, $class)) {
                    throw new Exception("array item must be subclass of $class");
                }
            }
        }

        $this->keys = array_keys($array);
        $this->values = array_values($array);
        $this->count = count($array);
        $this->current = 0;
        $this->class = $class;
    }

    public function current()
    {
        return $this->values[$this->current];
    }

    public function next()
    {
        $this->current++;
    }

    public function key()
    {
        return $this->keys[$this->current];
    }

    public function valid()
    {
        return $this->current < $this->count;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function offsetExists($offset)
    {
        return in_array($offset, $this->keys);
    }

    public function offsetGet($offset)
    {
        if (false === ($key = array_search($offset, $this->keys))) {
            return null;
        }

        return $this->values[$key];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        if ($value === null) {
            $this->offsetUnset($offset);
            return;
        }

        if ($this->class && !is_a($value, $this->class)) {
            throw new Exception("value must be subclass of {$this->class}");
        }

        if (false === ($index = array_search($offset, $this->keys))) {
            // 不存在，则在末尾追加
            $this->keys[] = $offset;
            $this->values[] = $value;
        } else {
            $this->keys[$index] = $offset;
            $this->values[$index] = $value;
        }

        $this->count++;
    }

    public function offsetUnset($offset)
    {
        if ($index = array_search($offset, $this->keys)) {
            unset($this->values[$index]);
            $this->count--;
        }
    }

    public function getArrayCopy(): array
    {
        return array_combine($this->keys, $this->values);
    }
    
    public function count()
    {
        return $this->count;
    }

    public function first()
    {
        return $this->values[0] ?? null;
    }

    public function last()
    {
        if (!$this->count) {
            return null;
        }
        return $this->values[$this->count - 1];
    }
}
