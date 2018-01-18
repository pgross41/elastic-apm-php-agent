<?php
namespace PhilKra\Stores;

use \PhilKra\Events\Transaction;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;

/**
 *
 * Store for the Transaction Events
 *
 */
class TransactionsStore implements \JsonSerializable {

  /**
   * HashMap of Transactions
   *
   * @var array of \PhilKra\Events\Transaction
   */
  private $store = [];

  /**
   * Register a Transaction
   *
   * @throws \PhilKra\Exception\Transaction\DuplicateTransactionNameException
   *
   * @param \PhilKra\Events\Transaction $transaction
   *
   * @return void
   */
  public function register( Transaction $transaction ) {
    $name = $transaction->getTransactionName();

    // Do not override the
    if( isset( $this->store[$name] ) === true ) {
      throw new DuplicateTransactionNameException( $name );
    }

    // Push to Store
    $this->store[$name] = $transaction;
  }

  /**
   * Fetch a Transaction from the Store
   *
   * @param final string $name
   *
   * @return mixed: \PhilKra\Events\Transaction | null
   */
  public function fetch( string $name ) {
    return $this->store[$name] ?? null;
  }

  /**
   * Is the Store Empty ?
   *
   * @return bool
   */
  public function isEmpty() : bool {
    return empty( $this->store );
  }

  /**
   * Serialize the Transactions Events Store
   *
   * @return array
   */
  public function jsonSerialize() : array {
    return array_values( $this->store );
  }

}