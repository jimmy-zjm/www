<?php
/**
 * 错误、异常处理类,可以自定义错误的接口
 * ErrorAndException::start(function($code, $message, $file, $line){});
 * @author  Grass
 * @date 2015-12-14
 */
class ErrorAndException{
    private static $display;
    private static $debug;
    /**
     * 开启错误处理
     * @access public
     */
    public static function start($callBack='', $debug=true){
        error_reporting(0);
        self::$debug = $debug;
        if(!$debug){
            //非调试模式, 将除了E_NOTICE级别的错误全部记录在文件或者数据库
            $callBack = function($code, $message, $file, $line){
                //code......................
                //写日志
                $arr = array(
                        E_NOTICE  => 'E_NOTICE',
                        E_WARNING => 'E_WARNING',
                        E_ERROR   => 'E_ERROR',
                    );
                $fp = fopen('error.log',"a");
                flock($fp, LOCK_EX) ;
                $str = '========================'.date('Y-m-d H:i:s').'========================='.PHP_EOL;
                if(isset($arr[$code])){
                    $code = $arr[$code];
                }
                $str.= '错误级别：'.$code.PHP_EOL;
                $str.= '错误信息：'.$message.PHP_EOL;
                $str.= '文件：'.$file.PHP_EOL;
                $str.= '行号：'.$line.PHP_EOL;
                fwrite($fp,$str);
                flock($fp, LOCK_UN);
                fclose($fp);
            };
        }

        //设置异常的处理器
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));

        //设置错误的处理器
        set_error_handler(array(__CLASS__, 'errorHandler'));
        //注册脚本结束的处理器(致命错会导致脚本结束)
        register_shutdown_function(array(__CLASS__, 'errorFatalHandler'));
        if(empty($callBack)){
            self::$display = function($code, $message, $file, $line){
                echo <<<HTML
                <!DOCTYPE html">
                <html><head>
                <meta carset="utf-8">
                <title>系统发生错误</title>
                <style type="text/css">
                *{ padding: 0; margin: 0; }
                html{ overflow-y: scroll; }
                body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
                img{ border: 0; }
                .error{ padding: 24px 48px; }
                .face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
                h1{ font-size: 32px; line-height: 48px; }
                .error .content{ padding-top: 10px}
                .error .info{ margin-bottom: 12px; }
                .error .info .title{ margin-bottom: 3px; }
                .error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
                .error .info .text{ line-height: 24px; }
                .copyright{ padding: 12px 48px; color: #999; }
                .copyright a{ color: #000; text-decoration: none; }
                </style>
                </head>
                <body>
                <div class="error">
                <p class="face">:(</p>
                <h1>{$message}</h1>
                <div class="content">
                    <div class="info">
                        <div class="title">
                            <h3>错误位置</h3>
                        </div>
                        <div class="text">
                            <p>FILE: {$file} &#12288;LINE: {$line}</p>
                        </div>
                    </div>
                </div>
                </div>
                </body>
                </html>
HTML;
            };
        }else{
            self::$display = $callBack;
        }
    }


    /**
     * 错误处理器
     * @return void
     */
    public static function errorHandler($errNo, $errStr, $errFile, $errLine){
        call_user_func(self::$display, $errNo, $errStr, $errFile, $errLine);
    }

    /**
     * 致命错误处理器
     * @return void
     */
    public static function errorFatalHandler(){
        ob_start();
        $arr = error_get_last();//获取最后一次的错误信息
        if(!empty($arr)){
            ob_end_clean();//清空输出缓冲区
            call_user_func(self::$display, $arr['type'], $arr['message'], $arr['file'], $arr['line']);
        }
    }

    /**
     * 异常处理器
     * @param  Exception $e 异常对象
     * @return void
     */
    public static function exceptionHandler(Exception $e){
        call_user_func(
                self::$display,
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
    }



}