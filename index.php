<?php
include_once 'inc/functions.php';
include_once 'inc/dbConnect.php';
include_once 'inc/Session.php';

	SecureSession::create();
	$logged_in = login_check($db);

include("components/pagebegin.php"); ?>
	<body>
		<div class="overlay">
			<section id="content-main">
				<div class="vertical-align-wrapper">
					<div class="vertical-center content-main-align">
						<h2>This is blohaute.</h2>
				<p>The mobile styling service that <br>brings the blowdry salon right to your door.<br>Beautiful hair with the click of a button.</p>
				<?php if($logged_in) { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking_in')\">book now</button>"; }
				 else { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking')\">book now</button>"; } ?>
					</div>
				</div>
			</section>
			<header class="clear">
				<a href="index.php"><h1 class="logo">blohaute</h1></a>
			</header>
			<?php include("components/userbar.php"); ?>
			<!-- <div class="clear"> -->
				<?php include("components/nav.php"); ?>
			<!-- </div> -->
			<section id="content-main-left">
				<h2>This is blohaute.</h2>
				<p>The mobile styling service that <br>brings the blowdry salon right to your door.<br>Beautiful hair with the click of a button.</p>
				<?php if($logged_in) { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking_in')\">book now</button>"; }
				 else { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking')\">book now</button>"; } ?>
			</section>
			<?php include("components/footer.php"); ?>
		</div>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-29437431-2', 'auto');
			ga('send', 'pageview');
		</script>
	</body>
	<?php include("components/booking-form.php"); ?>
	<script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/jquery-1.11.1.min.js"></script> 
    <script type="text/JavaScript" src="js/jquery-ui.min.js"></script> 
    <script type="text/JavaScript" src="js/jquery.validate.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/jquery-ui.min.css">
    <script src="js/TweenLite.min.js"></script>
	<script src="js/TimelineLite.min.js"></script>
	<script src="js/plugins/CSSPlugin.min.js"></script>
    <script type="text/javascript" src="js/bookingForm.js"></script>
    <script type="text/javascript" src="js/userForm.js"></script>
    <script>
	    $(function() {
	    	$("#date").datepicker({minDate: 0, dateFormat: 'yy-mm-dd'});
	    	$("#event_req_date").datepicker({minDate: 0, dateFormat: 'yy-mm-dd'});
	    });
	    $("#booking-form").validate({
			onfocusout: false,
			rules: {
				cnewpw: {
					equalTo: "#newpw"
				},
				ccnum: {
					creditcard: true
				}
			},
			errorPlacement: function(error, element) {
				var target = element.parent().parent().next('.response');
				// if (target.html().length == 0) {
					error.appendTo(target);
				// }
			},
			messages: {
				fname: "Your first name is required",
				lname: "Your last name is required",
				phone: "A 10 digit phone number is required",
				product: "Please select a service or package",
				expiry: "Expiration Date is required",
				ccv: "CCV code is required"
			}
		});
		jQuery.extend(jQuery.validator.messages, {
		    equalTo: "Passwords do not match"
		});
	</script>
</html>