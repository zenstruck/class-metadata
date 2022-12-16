<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sub;

use Zenstruck\Alias;
use Zenstruck\Metadata;

#[Alias('class-c')]
#[Metadata('foo', 'bar')]
final class ClassC
{
}
