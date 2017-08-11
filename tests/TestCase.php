<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function assertExecutableExists($executable, $command = null, $message = null)
    {
        if (is_null($command)) {
            $command = "$executable --help";
        }

        exec($command, $output, $exitCode);

        if (is_null($message)) {
            $message = "Could not run `" . basename($executable) . "`, please install it or grant executable privileges with `chmod +x $executable`";
        }

        $this->assertEquals(
            0, $exitCode, $message
        );
    }
}
