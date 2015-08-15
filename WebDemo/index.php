<?php
require_once(__DIR__ . "/../data/Framework.Config.php");
require_once(__DIR__ . '/../include/Web.Framework/Core.Class.php');

Web\Framework\Core::InitFramework(__DIR__);

/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/20
 * Time: 15:06
 */
final class WebRouter extends \Web\Framework\Router
{
    /**
     * @var \Web\Controller\ControllerBase
     */
    public static $Context;

    public static function DoAction()
    {
        parent::Initialize();

        try {

            self::$ControllerInfo = parent::ParseController();

            if (empty(self::$ControllerInfo['name'])
                || empty(self::$ControllerInfo['class'])
            ) {
                \Web\Utils\WebUtils::JSAlert('错误的调用', \Web\Utils\WebUtils::REDIRECT_NO_REDIRECT);
            }

            $name = self::$ControllerInfo['name'];
            $class = self::$ControllerInfo['class'];

            $ControllerClass = self::GetControllerClass($name, $class);

            if ($ControllerClass === false) {
                \Web\Utils\WebUtils::JSAlert('不存在处理方法', \Web\Utils\WebUtils::REDIRECT_NO_REDIRECT);
                self::ResponseEnd();
            }


            self::$Context = new $ControllerClass();

            self::$Context->Initialize($name, $class);

            self::$TemplatePath = self::ParseTemplatePath($name, $class);

            if (self::$Context->GetDoActionName() === false) {
                self::$Context->Execute();
            } else {
                self::$Context->ProcessDoAction();
            }

            if (self::$Context->ShowTemplate) {


                if (is_file(self::$TemplatePath)) {
                    include(self::$TemplatePath);
                } else {

                    \Web\Utils\NDebug::VerDump(self::$TemplatePath);

                    \Web\Utils\WebUtils::JSAlert('不存在模版', \Web\Utils\WebUtils::REDIRECT_NO_REDIRECT);

                }
            }

            self::$Context->ResponseEnd();

        } catch (Exception $ex) {
            \Web\Utils\WebUtils::Alert($ex->getMessage());
        }
    }


}

WebRouter::DoAction();