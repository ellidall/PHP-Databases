<?php
declare(strict_types = 1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

(new Dotenv())->load(__DIR__.'/../.env');

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
