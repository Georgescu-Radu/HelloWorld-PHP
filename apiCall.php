<?php

require_once(__DIR__ . '/vendor/autoload.php');

use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Payment;

session_start();

function makeAPICall()
{

	// Create SDK instance
	$config = include('config.php');
	$dataService = DataService::Configure(array(
		'auth_mode' => 'oauth2',
		'ClientID' => $config['client_id'],
		'ClientSecret' => $config['client_secret'],
		'RedirectURI' => $config['oauth_redirect_uri'],
		'scope' => $config['oauth_scope'],
		'baseUrl' => "development"
	));

	/*
	 * Retrieve the accessToken value from session variable
	 */
	$accessToken = $_SESSION['sessionAccessToken'];

	/*
	 * Update the OAuth2Token of the dataService object
	 */
	$dataService->updateOAuth2Token($accessToken);
	$customers = $dataService->Query("SELECT * FROM Customer");
	if (!is_null($customers)) {
		foreach ($customers as $customer) {
			echo "Customer Id: " . $customer->Id . "<br>";
			echo "Customer Name: " . $customer->DisplayName . "<br>";
			if (isset($customer->PrimaryEmailAddr->Address)) {
				echo "Customer Email: " . $customer->PrimaryEmailAddr->Address . "<br>";
			}

			$invoices = $dataService->Query("SELECT * FROM Invoice WHERE CustomerRef = '" . $customer->Id . "'");
			if (!is_null($invoices)) {
				foreach ($invoices as $invoice) {
					echo "Invoice Number: " . $invoice->DocNumber . "<br>";
					echo "Invoice Amount: " . $invoice->TotalAmt . "<br>";
					echo "<br>";
				}
			}

			$payments = $dataService->Query("SELECT * FROM Payment WHERE CustomerRef = '" . $customer->Id . "'");
			if (!is_null($payments)) {
				foreach ($payments as $payment) {
					if (isset($payment->TxnNumber)) {
						echo "Payment Number: " . $payment->TxnNumber . "<br>";
					}
					echo "Payment Amount: " . $payment->TotalAmt . "<br>";
					echo "Payment Date: " . $payment->TxnDate . "<br>";
					echo "<br>";
				}
			}

			echo "<br>";
		}
	}


}

$result = makeAPICall();

?>
