<?php
include_once '../inc/Member.php';
include_once("../inc/phpmailer.class.php");

function sendBookingConfirmation($db, $member_id, $member, $address_id, $coupon_id, $product_id, $product_name, $price, $date, $time) {
	$address = getMemberAddress($db, $member_id, $address_id);
	$mail = new PHPMailer();

try {
    $mail->IsSMTP();
    $mail->SMTPDebug = 1;
    $mail->Host = 'smtpout.secureserver.net';
    $mail->SMTPAuth = true;
    $mail->Port = 25;
    $mail->Username = 'bookings@blohaute.com';
    $mail->Password = 'Soltwisch22';

    $mail->Timeout = 36000;
    $mail->Subject = 'Blohaute Order';
    $from = 'bookings@blohaute.com';
    $mail->From = $from;
    $mail->FromName = 'blohaute';
    $mail->AddReplyTo('blohaute', $from);
    $to = $member['email'];
    $mail->AddAddress($to, '');
    $mail->Body = 'Thank you for choosing Blohaute!  We will contact you soon if there is a scheduling conflict.  Your order details are listed below.
<h4>Order Information</h4>
<div style="margin-bottom:20px;">
    <label style="width:110px;font-weight:bold;display:inline-block;">Purchased By:</label> ' . $member['fname'].' '.$member['lname'] . '<br />
    <label style="width:110px;font-weight:bold;display:inline-block;">Purchased On:</label> ' . date('M d, Y', time()) . '<br />
    <label style="width:110px;font-weight:bold;display:inline-block;vertical-align:top;">Address:</label>
    <div style="display:inline-block;">' . $address['address'] .' '.$address['aptnum'].' <br />' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'] . '</div>
</div>
<table style="width:100%;">
    <tr style="background-color:#C9C9C9;font-weight:bold;">
        <td>Item</td>
        <td>Reservation</td>
        <td>Price</td>
        <td></td>
    </tr>
    <tr>
        <td>' . $product_name . '</td>
        <td>' . $date . ' at ' . $time . '</td>
        <td>$' . $price . '</td>
        <td></td>
    </tr>
</table>';
error_log('finished mail call');
    $mail->IsHTML(true);
    if(!$mail->Send()) {
    	error_log("Error sending mail ".$mail->ErrorInfo);
    }
    error_log("past send");
    } catch (phpmailerException $e) {
	  error_log($e->errorMessage()); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  error_log($e->getMessage()); //Boring error messages from anything else!
	}
}