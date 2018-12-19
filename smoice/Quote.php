<?php

namespace smoice;

class Quote extends Base
{
  public $number;
  public $customerId;
  public $date;
  public $validTill;
  public $currency;
  public $netTotal;
  public $grossTotal;
  public $textBeforeQuote;
  public $textAfterQuote;
  public $status;
  public $details;
}
