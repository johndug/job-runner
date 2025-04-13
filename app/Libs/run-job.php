<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../bootstrap/app.php';

class RunJob
{
    private $className;
    private $methodName;
    private $params;

    public function __construct()
    {
        if (count($_SERVER['argv']) < 2) {
            die("Usage: php run-job.php ClassName [methodName] \"param1,param2\"\n");
        }

        $this->className = $_SERVER['argv'][1];
        $this->methodName = $_SERVER['argv'][2] ?? 'handle';
        $this->params = isset($_SERVER['argv'][3]) ? explode(',', $_SERVER['argv'][3]) : [];

        $this->execute();
    }

    private function execute()
    {
        $fullClassName = "App\\Libs\\" . $this->className;

        if (!class_exists($fullClassName)) {
            die("Class {$this->className} not found in Libs directory\n");
        }

        $class = new $fullClassName();

        if (!method_exists($class, $this->methodName)) {
            die("Method {$this->methodName} not found in class {$this->className}\n");
        }

        call_user_func_array([$class, $this->methodName], $this->params);
    }
}

$runJob = new RunJob();