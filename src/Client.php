<?php

namespace LSN\ipmitool;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Client
{
    protected $process;
    protected $config;
    protected $cwd;

    public function __construct(Process $process, Config $config)
    {
        $this->process = $process;
        $this->config = $config;
    }

    public function run(array $command, callable $callback = null)
    {
        $config = $this->getConfig();
        $env = $config->getEnvironmentVariables();
        $command = array_merge($config->generateBaseCommand(), $command);
        if ($config->getCwd() !== '') {
            $this->process->setWorkingDirectory($config->getCwd());
        }
        $this->process->setCommandLine($command);
        $timeout = $config->getTimeout();
        if ($timeout > 0) {
            $this->process->setTimeout($timeout);
        }
        $this->process->run($callback, $env);
        if (!$this->process->isSuccessful()) {
            throw new ProcessFailedException($this->process);
        }

        return $this->process->getOutput();
    }

    /**
     * @codeCoverageIgnore
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @param $process
     * @codeCoverageIgnore
     * @return Client
     */
    public function setProcess($process): Client
    {
        $this->process = $process;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @codeCoverageIgnore
     * @return Client
     */
    public function setConfig(Config $config): Client
    {
        $this->config = $config;
        return $this;
    }
}
