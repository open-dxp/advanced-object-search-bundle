<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Tests\Factory;

use OpenDxp\Model\DataObject\Book;
use OpenDxp\Test\Factory\AbstractDataObjectFactory;

final class BookFactory extends AbstractDataObjectFactory
{
    public static function class(): string
    {
        return Book::class;
    }
}
