<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Assert extends PHPUnit_Framework_Assert
{
    /**
     * Alias for PHPUnit's assertions methods
     *
     * The key is the alias name, which can be used as a method, and the value is the full name of the (PHPUnit) method
     *
     * @var array
     */
    static private $_alias = array(
        'eq'        => 'assertEquals',
        'sm'        => 'assertSame',
    );

    /**
     * Calls the method specified by its name (minus "assert") or alias (static calls)
     *
     * @param string $name    name of the method to call (minus prefix "assert") or alias
     * @param array  $args    parameters for the method
     *
     * @throws Exception      method doesn't exists
     * @return void
     */
    static public function __callStatic($name, array $args)
    {
        self::_call($name, $args);
        $class = __CLASS__;
        return new $class;
    }

    /**
     * Calls the method specified by its name (minus "assert") or alias (instantiated calls)
     *
     * @param string $name    name of the method to call (minus prefix "assert") or alias
     * @param array  $args    parameters for the method
     *
     * @throws Exception      method doesn't exists
     * @return void
     */
    public function __call($name, array $args)
    {
        self::_call($name, $args);
        return $this;
    }

    /**
     * Calls the method specified by its name (minus "assert") or alias
     *
     * @param string $name    name of the method to call (minus prefix "assert") or alias
     * @param array  $args    parameters for the method
     *
     * @throws Exception      method doesn't exists
     * @return void
     */
    static private function _call($name, array $args)
    {
        if (isset(self::$_alias[$name]))
        {
            $name = self::$_alias[$name];
        }
        else
        {
            $name = 'assert' . ucfirst($name);
        }

        if (!method_exists(__CLASS__, $name))
        {
            throw new Exception('Unknown assert method:' . $name);
        }

        $class = __CLASS__;
        switch (count($args))
        {
            case 0:
                $class::$name();
                break;
            case 1:
                $class::$name($args[0]);
                break;
            case 2:
                $class::$name($args[0], $args[1]);
                break;
            case 3:
                $class::$name($args[0], $args[1], $args[2]);
                break;
            default:
                call_user_func_array(array($class, $name), $args);
                break;
        }
    }
}