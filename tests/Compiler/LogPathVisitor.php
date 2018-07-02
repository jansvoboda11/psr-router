<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Compiler;

use Svoboda\PsrRouter\Compiler\PartsVisitor;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

/**
 * Visits path parts and logs the order.
 */
class LogPathVisitor extends PartsVisitor
{
    /**
     * Holds the logged messages.
     *
     * @var string[]
     */
    private $log;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->log = [];
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path): void
    {
        $this->addLog("Entering attribute " . $path->getName());
    }

    /**
     * @inheritdoc
     */
    public function leaveAttribute(AttributePath $path): void
    {
        $this->addLog("Leaving attribute " . $path->getName());
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path): void
    {
        $this->addLog("Entering optional");
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path): void
    {
        $this->addLog("Leaving optional");
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path): void
    {
        $this->addLog("Entering static " . $path->getStatic());
    }

    /**
     * @inheritdoc
     */
    public function leaveStatic(StaticPath $path): void
    {
        $this->addLog("Leaving static " . $path->getStatic());
    }

    /**
     * Returns the path log.
     *
     * @return string[]
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * Adds the message to the log.
     *
     * @param string $message
     */
    private function addLog(string $message): void
    {
        $this->log[] = $message;
    }
}
