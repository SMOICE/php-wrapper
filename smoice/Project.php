<?php

namespace smoice;

class Project extends Base
{
  /*
   * int
   * references a customer by id
   * required
   */
  public $customerId;

  /*
   * string
   * full name of the customer
   * read only
   */
  public $customerName;

  /*
   * string
   * the number by which the project is known
   * read only
   */
  public $number;

  /*
   * string
   * the name of the project
   * required
   */
  public $name;

  /*
   * float
   * the vat rate applicable for this project in per cent
   * optional
   */
  public $vatRate;

  /*
   * string
   * hours budgeted for this project
   * optional
   */
  public $hoursBudget;

  /*
   * string
   * accumulated hours spent on the project so far
   * read only
   */
  public $hoursSpent;

  /*
   * string
   * how many hours are left of the budget of the project
   * read only
   */
  public $hoursLeft;

  /*
   * boolean
   * is the project done
   * optional
   */
  public $done;

  public $costForHours;
  public $hoursLeftWithNegatives;

}
