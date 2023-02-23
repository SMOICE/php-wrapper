<?php

namespace smoice;

class Payment extends Base
{
  public \DateTime $date;
  public float $amount;
  public string $bookingId;
  public bool $automatch;
  public string $description;
}
