<?php

namespace smoice;

class Invoice extends Base
{
  public $number;
  public $customerId;
  public $name;
  public $name2;
  public $address;
  public $customerEmail;
  public $customerName;
  public $date;
  public $dueDate;
  public $cancelationDate;
  public $currency;
  public $netTotal;
  public $grossTotal;
  public $cancelled;
  public $cancellationId;
  public $cancellationFor;
  public $details;

  public $orderNumber;
  public $skontoRate;
  public $dueDateSkonto;
  public $directDebit;
  public $footer;
  public $advancePayment;
  public $salutation;
  public $textBeforeInvoice;
  public $textAfterInvoice;
  public $proforma;

  public $lastSendDone;
  public $lastSendWith;
  public $lastSend;
}
