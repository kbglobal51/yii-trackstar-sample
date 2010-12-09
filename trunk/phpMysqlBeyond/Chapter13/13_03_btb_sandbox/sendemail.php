<?php

	$to = "nobody@somedomain.com";

	// Windows may not handle this format well
	// $to = "Recipient Name <nobody@somedomain.com>";

	// multiple recipients
	// $to = "nobody@somedomain.com, nobody@somedomain.com";
	// $to = "Recipient Name <nobody@somedomain.com>, nobody@somedomain.com";

	$subject = "Mail Test at ".strftime("%T", time());

	$message = "This is a test.";
	// Optional: Wrap lines for old email programs
	// wrap at 70/72/75/78
	$message = wordwrap($message,70);
	
	$from = "somebody@somedomain.com";
	$headers = "From: {$from}";
	
	$result = mail($to, $subject, $message, $headers);
	echo $result ? 'Sent' : 'Error';
  
?>