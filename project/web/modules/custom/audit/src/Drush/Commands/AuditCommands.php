<?php

namespace Drupal\audit\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 */
final class AuditCommands extends DrushCommands {

  use AutowireTrait;

  /**
   * Constructs an AuditCommands object.
   */
  public function __construct(
    private readonly Token $token,
  ) {
    parent::__construct();
  }

  /**
   * Command description here.
   */
  #[CLI\Command(name: 'audit:get-deleted-records', aliases: ['foo'])]
  #[CLI\Argument(name: 'limit', description: 'Argument description.')]
  #[CLI\Option(name: 'option-name', description: 'Option description')]
  #[CLI\Usage(name: 'audit:command-name foo', description: 'Usage description')]
  public function commandName($arg1, $options = ['option-name' => 'default']) {
    $this->logger()->success(dt('Achievement unlocked.'));
  }

  /**
   * An example of the table output format.
   */
  #[CLI\Command(name: 'audit:token', aliases: ['token'])]
  #[CLI\FieldLabels(labels: [
    'group' => 'Group',
    'token' => 'Token',
    'name' => 'Name'
  ])]
  #[CLI\DefaultTableFields(fields: ['group', 'token', 'name'])]
  #[CLI\FilterDefaultField(field: 'name')]
  public function token($options = ['format' => 'table']): RowsOfFields {
    $all = $this->token->getInfo();
    foreach ($all['tokens'] as $group => $tokens) {
      foreach ($tokens as $key => $token) {
        $rows[] = [
          'group' => $group,
          'token' => $key,
          'name' => $token['name'],
        ];
      }
    }
    return new RowsOfFields($rows);
  }

}
