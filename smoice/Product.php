<?php

namespace smoice;

class Product extends Base
{
  /*
   * int
   * references a customer by id
   * required
   */
  public $customerId;

  /*
   * int
   * references a project by id
   * optional
   */
  public $projectId;

  /*
   * int
   * references a user by id
   * optional, defaults to the api user
   */
  public $userId;

  /*
   * string
   * the date a product was created or a service was rendered
   * required
   */
  public $date;

  /*
   * float
   * the quantity of the product or service
   * optional, defaults to 1
   */
  public $quantity;

  /*
   * string
   * the description of the product or service
   * optional
   */
  public $description;

  /*
   * object (see Price.php)
   * price of a quantity of one of the product or service
   * required
   * will also accept a float, which will be interpreted as a price in EUR
   */
  public $price;

  /*
   * float
   * the vat rate of the product or service
   * required
   */
  public $vatRate;

  public function __construct ( $mapObjectOrArray  = null )
  {
    if ( is_object($mapObjectOrArray) && is_object($mapObjectOrArray->price) )
      $mapObjectOrArray->price = new Price($mapObjectOrArray->price);
      
    parent::__construct($mapObjectOrArray);
  }

}

?>
