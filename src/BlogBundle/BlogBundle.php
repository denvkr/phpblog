<?php

namespace BlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use BlogBundle\DependencyInjection\BlogExtension;

class BlogBundle extends Bundle
{
    public function getContainerExtension(){
        return new BlogExtension();
    }
}
