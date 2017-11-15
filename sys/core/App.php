<?php
/**
  * @author guorunhe<guorunhe@foxmail.com>
  * @date   17/11/05
  */

namespace core; // 定义命名空间.

use core\Config; // 使用配置类.
use core\Router; // 使用路由.

/**
  * 框架启动类.
  */
class App
{

    public static $router; // 定义一个静态路由实例.

    // 启动.
    public static function run()
    {
        self::$router = new Router();
        self::$router->setUrlType(Config::get('url_type')); // 读取配置并设置路由类型.
        $url_array = self::$router->getUrlArray(); // 获取经过路由类处理生成的路由数组.
        self::dispatch($url_array); // 根据路由数组分发路由.
    }

    // 路由分发.
    public static function dispatch($url_array = [])
    {
        $module = '';
        $controller = '';
        $action = '';
        if (isset($url_array['module'])) {
            // 若路由中存在module, 则设置当前模块.
            $module = $url_array['module'];
        } else {
            $module = Config::get('default_module'); // 不存在，则设置默认的模块(home).
        }
        if (isset($url_array['controller'])) {
            // 若路由中存在controller,则设置当前控制器, 首字母大写.
            $controller = ucfirst($url_array['controller']);
        } else {
            $controller = ucfirst(Config::get('default_controller'));
        }
        // 拼接控制器文件路径.
        $controller_file = APP_PATH . $module . DS . 'controller' . DS . $controller . 'Controller.php';
        if (isset($url_array['action'])) {
            $action = $url_array['action'];
        } else {
            $action = Config::get('default_action');
        }
        
        // 判断控制器文件是否存在.
        if (file_exists($controller_file)) {
            require $controller_file; // 引入该控制器.
            $className = 'module\controller\IndexController'; // 命名空间字符串示例.
            $className = str_replace('module', $module, $className); // 使用字符串对应的模块名和控制器名.
            $className = str_replace('IndexController', $controller . 'Controller', $className);
            $controller = new $className;
            $controller->setTpl($action);
            // 判断访问的方法是否存在.
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                die('该方法不存在.');
            }
        } else {
            die('该控制器不存在.');
        }
    }
}
