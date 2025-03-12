<?php

namespace smoice;

class Wrapper
{
  private $refreshToken;
  private $url;
  private $body;

  public function __construct(string $refreshToken, string $server = 'https://easy.smoice.de/')
  {
    $this->refreshToken = $refreshToken;
    $this->url = $server;
  }

  /*
   * ask for user information providing login credentials
   * result include api key for further requests
   */
  public function login(string $email, string $passwordHash)
  {
    return $this->executeRequest('login', 'POST', array('email' => $email, 'passwordHash' => $passwordHash));
  }

  /*
   * ask for user information providing
   */
  public function findUser()
  {
    return $this->executeRequest('user', 'GET');
  }










  /*
   * customer related methods
   */
  public function createCustomer(Customer $customer)
  {
    return $this->executeRequest('customers', 'POST', $customer);
  }

  public function findCustomer(int $id)
  {
    return $this->findOne('customers', $id);
  }

  public function findCustomerByNumber(string $number)
  {
    $result = $this->executeRequest('customers', 'GET', array('numbers' => array($number)));
    if (isset($result->errorCode))
      return $result;

    $row = reset($result);
    $customer = new Customer($row);

    return $customer;
  }

  public function findCustomers(array $ids = null)
  {
    return $this->findMany('customers', $ids);
  }

  /*
   * possible values for orderBy: number, name, phone, email
   * possible values for order: asc, desc
   */
  public function searchCustomers(string $searchValue, int $start, int $length, string $orderBy = 'name', string $order = 'asc')
  {
    $result = $this->executeRequest(
      'customers/search',
      'GET',
      array(
        'search' => $searchValue,
        'start' => $start,
        'length' => $length,
        'orderBy' => $orderBy,
        'order' => $order
      )
    );
    if (isset($result->errorCode))
      return $result;

    $customers = array();
    foreach ($result->customers as $row)
      $customers[] = new Customer($row);

    return array(
      'numberTotal' => (int) $result->numberTotal,
      'numberFiltered' => (int) $result->numberFiltered,
      'customers' => $customers
    );
  }

  public function updateCustomer(Customer $customer)
  {
    return $this->executeRequest('customers/' . $customer->id, 'PUT', $customer);
  }






  /*
   * project related methods
   */
  public function createProject(Project $project)
  {
    return $this->executeRequest('projects', 'POST', $project);
  }

  public function findProjects(array $ids = null)
  {
    return $this->findMany('projects', $ids);
  }

  public function findProject(int $id)
  {
    return $this->findOne('projects', $id);
  }

  public function updateProject(Project $project)
  {
    return $this->executeRequest('projects/' . $project->id, 'PUT', $project);
  }






  /*
   * contract related methods
   */
  public function createContract(Contract $contract)
  {
    return $this->executeRequest('contracts', 'POST', $contract);
  }

  public function findContracts()
  {
    return $this->findMany('contracts');
  }

  public function findContract(int $id)
  {
    return $this->findOne('contracts', $id);
  }

  public function updateContract(Contract $contract)
  {
    return $this->executeRequest('contracts/' . $contract->id, 'PUT', $contract);
  }





  /*
   * invoice related methods
   */
  public function createInvoice(
    int $customerId,
    array $details,
    string $textBefore = null,
    string $textAfter = null,
    bool $pricesIncludeVAT = false,
    bool $preview = false,
    string $dueDate = null,
    bool $proforma = false,
    string $orderNumber = null,
    bool $createOpenItem = true,
    string $invoiceNumber = null,
    string $invoiceType = 'standard',
    array $abschlaege = []
  ) {
    return $this->executeRequest(
      'invoices',
      'POST',
      array(
        'customerId' => $customerId,
        'textBefore' => $textBefore,
        'textAfter' => $textAfter,
        'pricesIncludeVAT' => $pricesIncludeVAT,
        'details' => $details,
        'preview' => $preview,
        'dueDate' => $dueDate,
        'proforma' => $proforma,
        'orderNumber' => $orderNumber,
        'createOpenItem' => $createOpenItem,
        'invoiceNumber' => $invoiceNumber,
        'invoiceType' => $invoiceType,
        'downPayments' => $abschlaege
      )
    );
  }

  public function getInvoicePDF(int $id, bool $includeBackground = true, bool $showProforma = false)
  {
    $result = $this->executeRequest('pdfinvoice/' . $id, 'GET', array(
      'includeBackground' => $includeBackground,
      'showProforma' => $showProforma
    ));
    if (isset($result->errorCode))
      return $result;

    return $result;
  }

  public function demoInvoice()
  {
    return $this->executeRequest('invoices/demo', 'GET');
  }

  public function findInvoice(int $id)
  {
    $result = $this->executeRequest('invoices/' . $id, 'GET');
    if (isset($result->errorCode))
      return $result;

    return new Invoice($result);
  }

  public function sendInvoice(int $id)
  {
    return $this->executeRequest('invoices/send/' . $id, 'GET');
  }

  public function sendInvoiceBySnailMail(int $id)
  {
    return $this->executeRequest('invoices/snail/' . $id, 'GET');
  }

  public function getERechnungXML(int $id)
  {
    return $this->executeRequest('invoices/erechnung/' . $id, 'GET');
  }

  public function findInvoices(string $fromDate = null, string $tillDate = null)
  {
    return $this->executeRequest('invoices', 'GET', array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function cancelInvoice(int $id, bool $leaveOpenItems = false)
  {
    $url = 'cancelinvoice/' . $id;
    if ($leaveOpenItems) {
      $url .= '?leaveOpenItems=1';
    }
    return $this->executeRequest($url, 'GET');
  }

  public function updateInvoice(Invoice $invoice)
  {
    return $this->executeRequest('invoices/' . $invoice->id, 'PUT', array(
      'name' => $invoice->name,
      'name2' => $invoice->name2,
      'address' => $invoice->address,
      'orderNumber' => $invoice->orderNumber,
      'skontoRate' => $invoice->skontoRate,
      'dueDateSkonto' => $invoice->dueDateSkonto,
      'dueDate' => $invoice->dueDate,
      'directDebit' => $invoice->directDebit,
      'advancePayment' => $invoice->advancePayment,
      'footer' => $invoice->footer,
      'salutation' => $invoice->salutation,
      'textBeforeInvoice' => $invoice->textBeforeInvoice,
      'textAfterInvoice' => $invoice->textAfterInvoice,
    ));
  }

  public function createInvoices(array $data)
  {
    $invoices = array();
    foreach ($data as $invoiceData) {
      $invoice = array(
        'customerId' => $invoiceData['customerId'],
        'textBefore' => $invoiceData['textBeforeInvoice'],
        'textAfter' => $invoiceData['textAfterInvoice'],
        'pricesIncludeVAT' => isset($invoiceData['pricesIncludeVAT']) ? $invoiceData['pricesIncludeVAT'] : false,
        'details' => $invoiceData['products'],
        'dueDate' => isset($invoiceData['dueDate']) ? $invoiceData['dueDate'] : null,
        'proforma' => false,
        'orderNumber' => isset($invoiceData['orderNumber']) ? $invoiceData['orderNumber'] : null,
        'createOpenItem' => isset($invoiceData['createOpenItem']) ? $invoiceData['createOpenItem'] : true
      );
      $invoices[] = $invoice;
    }

    return $this->executeRequest(
      'invoices',
      'POST',
      $invoices
    );
  }

  public function findUnmatchedDownpayments(int $customerId): array
  {
    return $this->executeRequest('invoices/downpayments', 'GET', array('customerId' => $customerId));
  }









  /*
   * methods related to quotes
   */
  public function sendQuote(int $id)
  {
    return $this->executeRequest('quotes/send/' . $id, 'GET');
  }

  public function findQuotes(string $fromDate = null, string $tillDate = null)
  {
    $result = $this->executeRequest('quotes', 'GET', array('fromDate' => $fromDate, 'tillDate' => $tillDate));
    if (isset($result->errorCode))
      return $result;

    $quotes = array();
    foreach ($result as $row)
      $quotes[] = new Quote($row);

    return $quotes;
  }


  public function createQuote(int $customerId, string $date, array $details, string $textBeforeQuote = null, string $textAfterQuote = null)
  {
    return $this->executeRequest(
      'quotes',
      'POST',
      array(
        'customerId' => $customerId,
        'date' => $date,
        'textBeforeQuote' => $textBeforeQuote,
        'textAfterQuote' => $textAfterQuote,
        'details' => $details,
      )
    );
  }

  public function findQuote(int $id)
  {
    $result = $this->findOne('quotes', $id);
    if (isset($result->errorCode))
      return $result;

    return new Quote($result);
  }

  // valid stati: open, accepted, declined
  public function changeQuoteStatus(int $id, string $status)
  {
    return $this->executeRequest('quotes/changeStatus/' . $id, 'PUT', array('status' => $status));
  }







  /*
   * methods related to open items
   */
  public function findOpenItems()
  {
    return $this->executeRequest('openitems', 'GET');
  }






  /*
   * event related methods
   */
  public function createEvent(Event $event)
  {
    return $this->executeRequest('events', 'POST', $event);
  }

  public function findEvent(int $id)
  {
    return $this->findOne('events', $id);
  }

  public function findEvents(string $fromDate = null, string $tillDate = null)
  {
    return $this->findMany('events', null, array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function updateEvent(Event $event)
  {
    return $this->executeRequest('events/' . $event->id, 'PUT', $event);
  }

  public function findEventParticipants(int $eventId)
  {
    return $this->executeRequest('participants', 'GET', array('eventId' => $eventId));
  }






  /*
   * product related methods
   */
  public function createProduct(Product $product)
  {
    return $this->executeRequest('products', 'POST', $product);
  }

  public function findProduct(int $id)
  {
    return $this->findOne('products', $id);
  }

  public function findProducts(array $ids = null, string $fromDate = null, string $tillDate = null)
  {
    return $this->findMany('products', $ids, array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function findUnbilledProducts()
  {
    return $this->findMany('products', null, array('unbilled' => true));
  }

  public function updateProduct(Product $product)
  {
    return $this->executeRequest('products/' . $product->id, 'PUT', $product);
  }

  public function deleteProduct($productOrProductId)
  {
    if (is_numeric($productOrProductId)) {
      return $this->executeRequest('products/' . $productOrProductId, 'DELETE');
    }

    return $this->executeRequest('products/' . $productOrProductId->id, 'DELETE');
  }






  /*
   * methods related to time entries
   */
  public function createTimeEntry(TimeEntry $timeEntry)
  {
    return $this->executeRequest('timeentries', 'POST', $timeEntry);
  }

  public function findTimeEntry(int $id)
  {
    $result = $this->executeRequest('timeEntries/' . $id, 'GET');
    if (isset($result->errorCode))
      return $result;

    return new TimeEntry($result);
  }

  public function findTimeEntries(array $ids = null, string $fromDate = null, string $tillDate = null)
  {
    $result = $this->executeRequest('timeentries', 'GET', array('ids' => $ids, 'fromDate' => $fromDate, 'tillDate' => $tillDate));
    if (isset($result->errorCode))
      return $result;

    $timeEntries = array();
    foreach ($result as $row)
      $timeEntries[] = new TimeEntry($row);

    return $timeEntries;
  }

  public function findOwnTimeEntries(array $ids = null, string $fromDate = null, string $tillDate = null)
  {
    $result = $this->executeRequest('timeentries/findOwn', 'GET', array('ids' => $ids, 'fromDate' => $fromDate, 'tillDate' => $tillDate));
    if (isset($result->errorCode))
      return $result;

    $timeEntries = array();
    foreach ($result as $row)
      $timeEntries[] = new TimeEntry($row);

    return $timeEntries;
  }

  public function updateTimeEntry(TimeEntry $timeEntry)
  {
    return $this->executeRequest('timeEntries/' . $timeEntry->id, 'PUT', $timeEntry);
  }

  public function deleteTimeEntry($timeEntryOrTimeEntryId)
  {
    if (is_numeric($timeEntryOrTimeEntryId))
      return $this->executeRequest('timeentries/' . $timeEntryOrTimeEntryId, 'DELETE');

    return $this->executeRequest('timeentries/' . $timeEntryOrTimeEntryId->id, 'DELETE');
  }






  /*
   * find the next automatic number for customers, projects, invoices, quotes
   */
  public function findPaymentsForInvoice(int $invoiceId)
  {
    $result = $this->executeRequest('payments', 'GET', array('invoiceId' => $invoiceId));
    if (isset($result->errorCode)) {
      return $result;
    }

    $payments = [];
    foreach ($result as $row) {
      $row->date = new \DateTime($row->date);
      $row->bookingId = (string)$row->bookingId;
      $payments[] = new Payment($row);
    }

    return $payments;
  }

  public function removePaymentsFromInvoice(int $invoiceId)
  {
    return $this->executeRequest('payments', 'DELETE', array('invoiceId' => $invoiceId));
  }

  public function addPaymentToInvoice(int $invoiceId, Payment $payment)
  {
    $data = [
      'invoiceId' =>  $invoiceId,
      'date' => $payment->date->format('Y-m-d'),
      'amount' => $payment->amount,
      'bookingId' => $payment->bookingId,
      'automatch' => (int)$payment->automatch,
      'description' => $payment->description,
    ];
    return $this->executeRequest('payments', 'POST', $data);
  }




  /*
   * find the next automatic number for customers, projects, invoices, quotes
   */
  public function nextNumber(string $type, bool $commitToDatabase = true)
  {
    return $this->executeRequest('nextNumber', 'POST', array(
      'type' => $type,
      'hochzaehlen' => $commitToDatabase
    ));
  }





  /*
   * helper methods
   */
  protected function findOne(string $endpoint, int $id)
  {
    $result = $this->executeRequest($endpoint . '/' . $id, 'GET');
    if (isset($result->errorCode))
      return $result;

    switch ($endpoint) {
      case 'projects':
        return new Project($result);
      case 'customers':
        return new Customer($result);
      case 'events':
        return new Event($result);
      case 'products':
        return new Product($result);
      case 'contracts':
        return new Contract($result);
      case 'quotes':
        return new Quote($result);
    }
  }

  protected function findMany(string $endpoint, array $ids = null, array $alternatives = null)
  {
    $params = array();
    if ($ids !== null) {
      $params['ids'] = $ids;
    }
    if ($alternatives !== null) {
      $params = array_merge($params, $alternatives);
    }

    $result = $this->executeRequest($endpoint . '/', 'GET', $params);
    if (isset($result->errorCode)) {
      return $result;
    }


    $return = array();
    foreach ($result as $row)
      switch ($endpoint) {
        case 'projects':
          $return[] = new Project($row);
          break;
        case 'customers':
          $return[] = new Customer($row);
          break;
        case 'events':
          $return[] = new Event($row);
          break;
        case 'products':
          $return[] = new Product($row);
          break;
        case 'contracts':
          $return[] = new Contract($row);
          break;
      }

    return $return;
  }

  final protected function executeRequest(string $request, string $method, $params = array())
  {
    //print_r($params);
    $url = $this->buildUrl($request);
    $this->buildJsonData($params);
    return $this->executeCurl($url, $method);
  }

  private function buildUrl(string $request)
  {
    $url = $this->url;
    if (substr($url, -1) != '/')
      $url .= '/';

    return $url . 'api/2.0/' . $request;
  }

  private function buildJsonData($params)
  {
    $this->body = array();
    foreach ($params as $key => $value)
      if ($value !== null)
        $this->body[$key] = $value;
    $this->body = json_encode($this->body);
  }

  private function executeCurl(string $url, string $method)
  {
    $ch = curl_init();

    $params = '';
    if ($method == 'GET' || $method == 'DELETE') {
      $params = http_build_query(json_decode($this->body));
    }

    if ($params > '') {
      $url .= '?' . $params;
    }

    $headers = array("Authorization: Bearer " . $this->refreshToken);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($method == 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
      $headers[] = 'Content-Type: application/json';
      $headers[] = 'Content-Length: ' . strlen($this->body);
    }

    if ($method == 'DELETE') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    if ($method == 'PUT') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //echo "$method: $url\n" . $this->body . "\n"; print_r($headers); //die();

    return json_decode(curl_exec($ch));
  }
}
