<?php
// src/Acme/TestBundle/AcmeTestBundle.php
namespace App\Gagan\Workflow;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GaganWorkflowBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
