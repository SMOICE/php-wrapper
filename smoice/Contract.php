<?php

namespace smoice;

class Contract extends Base
{
  /*
   * int
   * references a customer by id
   * required
   */
  public $customerId;

  /*
   * string
   * date when billing for this contract starts
   * required
   */
  public $billingStart;

  /*
   * string
   * the description of the contract as it appears on invoices
   * required
   */
  public $description;

  /*
   * float
   * the vat rate of the product or service
   * required
   */
  public $vatRate;

  /*
   * string
   * how often the contract is billed
   * possible values: monatlich, vierteljährlich, halbjährlich, jährlich, gesamte Laufzeit
   * required
   */
  public $billingCycle;

  /*
   * object (see Price.php)
   * price of a quantity of one of the product or service
   * required
   * will also accept a float, which will be interpreted as a price in EUR
   */
  public $price;

  /*
   * boolean
   * is contract billed automatically
   * optional
   */
  public $autoBilling;

  /*
   * int
   * how many days before the billing period is the contract billed automaticall
   * e.g. 7 means contract get billed automatically 1 week before start of billing period
   * optional
   */
  public $daysBeforeBillingPeriod;
  
}
