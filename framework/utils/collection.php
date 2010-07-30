<?php
namespace Lysine\Utils;

class Coll {
    protected $coll;

    /**
     * Interator接口
     *
     * @var mixed
     * @access protected
     */
    protected $has_next = false;

    public function __construct(array $elements = array()) {
        $this->coll = $elements;
    }

    /**
     * Iterator接口
     *
     * @access public
     * @return miexed
     */
    public function current() {
        return current($this->coll);
    }

    /**
     * Iterator接口
     *
     * @access public
     * @return mixed
     */
    public function key() {
        return key($this->coll);
    }

    /**
     * Iterator接口
     *
     * @access public
     * @return void
     */
    public function next() {
        $this->has_next = (next($this->coll) !== false);
    }

    /**
     * Iterator接口
     *
     * @access public
     * @return void
     */
    public function rewind() {
        $this->has_next = (reset($this->coll) !== false);
    }

    /**
     * Iterator接口
     *
     * @access public
     * @return void
     */
    public function valid() {
        return $this->has_next;
    }

    /**
     * Countable接口
     *
     * @access public
     * @return integer
     */
    public function count() {
        return count($this->coll);
    }

    /**
     * ArrayAccess接口
     *
     * @param mixed $offset
     * @access public
     * @return boolean
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->coll);
    }

    /**
     * ArrayAccess接口
     *
     * @param mixed $offset
     * @access public
     * @return mixed
     */
    public function offsetGet($offset) {
        return array_key_exists($offset, $this->coll)
             ? $this->coll[$offset]
             : false;
    }

    /**
     * ArrayAccess接口
     *
     * @param mixed $offset
     * @param mixed $val
     * @access public
     * @return void
     */
    public function offsetSet($offset, $val) {
        $this->coll[$offset] = $val;
    }

    /**
     * ArrayAccess接口
     *
     * @param mixed $offset
     * @access public
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->coll[$offset]);
    }

    /**
     * 返回并删除第一个元素
     *
     * @access public
     * @return mixed
     */
    public function shift() {
        return array_shift($this->coll);
    }

    /**
     * 把元素插入到数组第一位
     *
     * @param mixed $element
     * @access public
     * @return Ly_Coll
     */
    public function unshift($element) {
        $args = array_reverse(func_get_args());
        foreach ($args as $arg) array_unshift($this->coll, $arg);
        return $this;
    }

    /**
     * 返回并删除最后一个元素
     *
     * @access public
     * @return mixed
     */
    public function pop() {
        return array_pop($this->coll);
    }

    /**
     * 把元素插入到数组尾部
     *
     * @param mixed $element
     * @access public
     * @return Ly_Coll
     */
    public function push($element) {
        $args = func_get_args();
        foreach ($args as $arg) array_push($this->coll, $arg);
        return $this;
    }

    /**
     * 还原为数组
     *
     * @access public
     * @return array
     */
    public function toArray() {
        return $this->coll;
    }

    /**
     * 把每个元素作为参数传递给callback
     * 把所有的返回值以Ly_Coll方式返回
     *
     * @param callback $callback
     * @param array $args
     * @access public
     * @return Ly_Coll
     */
    public function map($callback, array $args = null) {
        if ($args) {
            return new self(array_map($callback, $this->coll, $args));
        } else {
            return new self(array_map($callback, $this->coll));
        }
    }

    /**
     * 把每个元素作为参数传递给callback
     * 和map不同，map会创建一个新的Ly_Coll
     * each是会修改自身
     *
     * @param callback $callback
     * @param mixed $more
     * @access public
     * @return Ly_Coll
     */
    public function each($callback, $more = null) {
        if ($more) {
            array_walk($this->coll, $callback, $more);
        } else {
            array_walk($this->coll, $callback);
        }
        return $this;
    }

    /**
     * 把数组中的每个元素作为参数传递给callback
     * 找出符合条件的值
     *
     * @param callback $callback
     * @access public
     * @return Ly_Coll
     */
    public function find($callback) {
        $find = array();

        foreach ($this->coll as $key => $el) {
            if (call_user_func($callback, $el)) $find[$key] = $el;
        }
        return new self($find);
    }

    /**
     * 把数组中的每个元素作为参数传递给callback
     * 过滤掉不符合条件的值
     *
     * @param callback $callback
     * @access public
     * @return Ly_Coll
     */
    public function filter($callback) {
        foreach ($this->coll as $key => $el) {
            if (!call_user_func($callback, $el))
                unset($this->coll[$key]);
        }
        return $this;
    }

    /**
     * 调用每个元素的方法
     * 把每次调用的结果以Ly_Coll类型返回
     *
     * @param string $fn
     * @param mixed $args
     * @access public
     * @return Ly_Coll
     */
    public function call($fn, $args = null) {
        $args = array_slice(func_get_args(), 1);

        $result = array();
        foreach ($this->coll as $key => $el) {
            $result[$key] = call_user_func_array(array($el, $fn), $args);
        }
        return new self($result);
    }

    /**
     * array_slice方法
     *
     * @param integer $offset
     * @param integer $length
     * @param boolean $preserve_keys
     * @access public
     * @return Ly_Coll
     */
    public function slice($offset, $length = null, $preserve_keys = false) {
        return new self(array_slice($this->coll, $offset, $length, $preserve_keys));
    }

    /**
     * array_splice方法
     *
     * @param integer $offset
     * @param integer $length
     * @access public
     * @return Ly_Coll
     */
    public function splice($offset, $length = 0) {
        $args = func_get_args();
        if (count($args) > 2) {
            $replace = $args[2];
            return new self(array_splice($this->coll, $offset, $length, $replace));
        }
        return new self(array_splice($this->coll, $offset, $length));
    }

    /**
     * array_reduce方法
     *
     * @param callable $function
     * @param mixed $initial
     * @access public
     * @return mixed
     */
    public function reduce($function, $initial = null) {
        return array_reduce($this->coll, $function, $initial);
    }
}
