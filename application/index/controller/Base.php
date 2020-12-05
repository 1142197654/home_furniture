<?php
/**
 * 基础控制器
 */
namespace app\index\controller;
//use app\index\services\v1\BaseService;
//use app\index\services\v1\RedisService;
//use app\index\services\v1\SysParaService;
use think\Container;
use think\Controller;
use think\Response;
use think\exception\HttpResponseException;

class Base extends Controller
{
    /**
     * 初始化方法
     */
    public function initialize()
    {
        debug('begin');

//        //系统名称
//        $sysName = getSysName();
//        //主系统授权URL
//        $sysUrl = config('sys_url');
//
//        //获取登录态id与系统id
//        $sessionId = cookie($sysName . '_login_status');
//        $sysId = cookie($sysName . '_id');
//        $dataSerialize = RedisService::get($sessionId);//读取redis登入态信息(序列化的数据)
//        if (!$sessionId || !$sysId || !$dataSerialize) {
//            $this->redirect($sysUrl . '?url=' . request()->url(true));
//        }
//
//        //反序列化登入态数据
//        $data = unserialize($dataSerialize, ['allowed_classes' => true]);
//
//        //路由组
//        $route_data = $data['userRoute']['route_data']??[];
//        $route_array = formatRoute($route_data);
//
//        //路由规则
//        $rule_data = $data['jurisdiction']['rule_data']??[];
//
//        $user_auth = createUserAuth($rule_data);
//        define('USER_AUTH',$user_auth);//定义用户权限常量，保存用户权限集合
//
//        //定义用户信息常量，保存用信息
//        define('USER_DATA', $data);
//        unset($data);
//
//        //定义栏目信息数组
//        if(!defined('RULE_LIST')){
//            $rule_array = formatRule($rule_data);
//            define('RULE_LIST',$rule_array);
//        }
//
//        //定义左侧菜单信息
//        if(!defined('LEFT_MENU')){
//            //组装出树形
//            $tree = createLeftMenuTree($route_array,RULE_LIST);
//
//            //生成html
//            $left_menu = createLeftMenuHtml($tree);
//            define('LEFT_MENU',$left_menu);
//        }
//
//        //取核心系统参数
//        $para_code_arr = [
//            SysParaService::$code_SysInfo,
//            SysParaService::$code_Browser,
//            SysParaService::$code_NoRuleUrl,
//            SysParaService::$code_NoRuleController,
//        ];
//        $sysPara = SysParaService::getSysParaByCodeArr($para_code_arr);
//
//        //系统信息
//        $sysInfo_json = $sysPara[SysParaService::$code_SysInfo]??'';
//        $sysInfo_arr = $sysInfo_json?json_decode($sysInfo_json,1):[];
//        $this->assign('sysInfo',$sysInfo_arr);
//
//        //无需验证权限的URL
//        $noRuleUrl_json = $sysPara[SysParaService::$code_NoRuleUrl]??'';
//        $noRuleUrl_arr = $noRuleUrl_json?json_decode($noRuleUrl_json,1):[];
//        if(!defined('NO_RULE_URL')){
//            define('NO_RULE_URL',$noRuleUrl_arr);
//        }
//
//        //无需验证权限的控制器
//        $noRuleController_json = $sysPara[SysParaService::$code_NoRuleController]??'';
//        $noRuleController_arr = $noRuleController_json?json_decode($noRuleController_json,1):[];
//
//        //支持的浏览器
//        $browser_json = $sysPara[SysParaService::$code_Browser]??'';
//        $browser_arr = $browser_json?json_decode($browser_json,1):[];
//
//        //判断浏览器
//        $browser = getBrowser();
//        if(!empty($browser_arr) && !in_array($browser,$browser_arr)){
//            exit('浏览器不支持，目前仅支持['.implode(',',$browser_arr).']浏览器！');
//        }
//
//        //取出3层结构
//        $module = request()->module();
//        $controller = request()->controller();
//        $action = request()->action(true);
//
//        //用于判断用户路由权限,权限格式：模块/控制器/方法名称
//        $url = $module . '/' . $controller . '/' . $action;
//        $url = urlCamelize($url,false);
//
//        //验证权限
//        if(in_array($controller,$noRuleController_arr) || in_array($url,$noRuleUrl_arr)){
//            //Common和AjaxFuns中的方法不需要验证权限
//        }else if (!checkAuth($url)) {
//            exit('对不起，您暂时没有权限访问['.$url.'],请联系上级！');
//        }
    }

    /**
     * 析构函数
     */
    public function __destruct(){}

    /**
     * 加载模板输出——重写
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $replace  模板替换
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        debug('end');

        //标记缓存是否开启
        $cacheFlag = BaseService::$cacheFlag;
        $this->assign('cacheFlag',$cacheFlag);
        //标记缓存的key
        $cacheKey = BaseService::$cacheKey;
        $this->assign('cacheKey',$cacheKey);

        //输出执行时间
        $exe_time = debug('begin','end',6).'s';
        $this->assign('exe_time',$exe_time);

        //输出内容消耗
        $exe_memory = debug('begin','end','m');
        $this->assign('exe_memory',$exe_memory);

        //输出当前url
        $_a = request()->action(true);
        $_a = ucfirst($_a);
        $actionUrl = getBaseUrl();
        $this->assign('actionUrl',$actionUrl);
        $this->assign('actionMethod',$_a);

        return $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * 重写操作成功跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @param string $button 按钮类型[1100:仅关闭弹窗;1101:关闭弹窗并刷新父级页面(默认);1102:关闭弹窗并刷新爷爷级页面;1010:跳转;1110:父级页面跳转;1199:关闭弹窗并调用js方法加载数据]
     * @return void
     */
    protected function success($msg = '', $url = null, $data = '', $wait = 10, array $header = [], $button='')
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Container::get('url')->build($url);
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
            'button' => $button,
        ];

        $type = $this->getResponseType();
        // 把跳转模板的渲染下沉，这样在 response_send 行为里通过getData()获得的数据是一致性的格式
        if ('html' == strtolower($type)) {
            $type = 'jump';
        }

        $response = Response::create($result, $type)->header($header)->options(['jump_template' => $this->app['config']->get('dispatch_success_tmpl')]);

        throw new HttpResponseException($response);
    }

    /**
     * 重写操作错误跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @param string $button 按钮类型[1100:仅关闭弹窗;1101:关闭弹窗并刷新父级页面(默认);1102:关闭弹窗并刷新爷爷级页面;1010:跳转;1110:父级页面跳转;1199:关闭弹窗并调用js方法加载数据]
     * @return void
     */
    protected function error($msg = '', $url = null, $data = '', $wait = 999, array $header = [],$button='1100')
    {
        $type = $this->getResponseType();
        if (is_null($url)) {
            $url = $this->app['request']->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app['url']->build($url);
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
            'button' => $button,
        ];

        if ('html' == strtolower($type)) {
            $type = 'jump';
        }

        $response = Response::create($result, $type)->header($header)->options(['jump_template' => $this->app['config']->get('dispatch_error_tmpl')]);

        throw new HttpResponseException($response);
    }

    /**
     * ajax返回成功的状态
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return \think\response\Json
     */
    protected function output_success($data=[],$msg='',$code=1){
        $return = ['code'=>$code,'data'=>$data,'msg'=>$msg];
        return json($return);
    }

    /**
     * ajax返回失败的数据
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
    protected function output_error($msg='',$data=[]){
        $code = 0;
        return $this->output_success($data,$msg,$code);
    }



}
