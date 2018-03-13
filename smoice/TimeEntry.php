<?php

namespace smoice;

class TimeEntry extends Base
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
   * the date of the time entry
   * required
   */
  public $date;

  /*
   * string
   * the start time
   * either start and end time or duration is required
   */
  public $startTime;

  /*
   * string
   * the end time
   * either start and end time or duration is required
   */
  public $endTime;

  /*
   * int
   * the duration of entry in minutes
   * either start and end time or duration is required
   */
  public $durationInMinutes;

  /*
   * string
   * the description of the product or service
   * optional
   */
  public $description;

  /*
   * object (see Price.php)
   * price of one hour 
   * required
   * will also accept a float, which will be interpreted as a price in EUR
   */
  public $pricePerHour;

  /*
   * float
   * the vat rate of the product or service
   * required
   */
  public $vatRate;

  public function __construct ( $mapObjectOrArray  = null )
  {
    if ( is_object($mapObjectOrArray) && is_object($mapObjectOrArray->pricePerHour) )
      $mapObjectOrArray->pricePerHour = new Price($mapObjectOrArray->pricePerHour);
      
    parent::__construct($mapObjectOrArray);
  }

}
