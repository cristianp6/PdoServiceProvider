<?php

namespace Csanquer\Silex\PdoServiceProvider\Config;

/*
 * License: GNU General Public License v3. 
 * http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * Credits: https://github.com/wakeless/transaction_pdo
 */

class PdoTransaction extends \PDO {

  // Database drivers that support SAVEPOINTs
  // https://en.wikipedia.org/wiki/Savepoint
  protected static $supportedDrivers = [
    'pgsql', 'mysql', 'mssql', 'dblib', 'sqlite',
  ];

  // Defines if current connection driver supports nestable calls
  protected $nestable = false;

  // The current transaction level
  protected $transLevel = 0;

  public function transaction( $call )
  {
    $this->nestable = in_array(
      $this->getAttribute( \PDO::ATTR_DRIVER_NAME ),
      self::$supportedDrivers
    );
    if($this->beginTransaction()) {
      try {
        $res = call_user_func( $call );
      } catch(\Exception $e) {
        $this->rollBack();
        throw $e;
      }
      if ($res) {
        if (!$this->commit()) {
          throw new \PDOExecption( 'Transaction not committed' );
        }
      } else {
        $this->rollBack();
      }
      return $res;
    } else {
      throw new \PDOExecption( 'Transaction not started' );
    }
  }

  public function beginTransaction()
  {
    if (!$this->nestable || $this->transLevel === 0) {
      $res = parent::beginTransaction();
      $this->transLevel++;
      return $res;
    } else {
      $this->exec("SAVEPOINT LEVEL{$this->transLevel}");
      $this->transLevel++;
      return true;
    }
  }

  public function commit()
  {
    $this->transLevel--;
    if (!$this->nestable || $this->transLevel === 0) {
      return parent::commit();
    } else {
      $this->exec("RELEASE SAVEPOINT LEVEL{$this->transLevel}");
      return true;
    }
  }

  public function rollBack()
  {
    $this->transLevel--;
    if (!$this->nestable || $this->transLevel === 0) {
      return parent::rollBack();
    } else {
      $this->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transLevel}");
      return true;
    }
  }

}
