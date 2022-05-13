<?php

namespace Zenstruck\Metadata\Tests\Fixture\Classes;

use Zenstruck\Alias;
use Zenstruck\Metadata;

#[Alias('class2')]
#[Metadata('key1', 'class2-value1')]
#[Metadata('key2', 2)]
class Class2
{
}
