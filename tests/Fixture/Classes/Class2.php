<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Metadata\Tests\Fixture\Classes;

use Zenstruck\Alias;
use Zenstruck\Metadata;

#[Alias('class2')]
#[Metadata('key1', 'class2-value1')]
#[Metadata('key2', 2)]
class Class2
{
}
