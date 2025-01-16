<?php

declare(strict_types=1);

namespace Drupal\audit;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a deletion record entity type.
 */
interface DeletionRecordInterface extends ContentEntityInterface, EntityChangedInterface {

}
