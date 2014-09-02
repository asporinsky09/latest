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
						<h2>this is blohaute</h2>
						<p>its a mobile on demand styling app<br> and you should sign up so we can get<br> rich and buy a yacht. sounds good.</p>
						<?php if($logged_in) { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking_in')\">book now</button>"; }
						 else { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking')\">book now</button>"; } ?>
					</div>
				</div>
			</section>
			<header class="clear">
				<a href="index.php"><h1 class="logo">blohaute</h1></a>
			</header>
			<?php include("components/userbar.php"); ?>
			<div class="clear">
				<?php include("components/nav.php"); ?>
			</div>
			<section id="content-main-left">
				<h2>this is blohaute</h2>
				<p>its a mobile on demand styling app<br> and you should sign up so we can get<br> rich and buy a yacht. sounds good.</p>
				<?php if($logged_in) { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking_in')\">book now</button>"; }
				 else { echo "<button class=\"btn-book btn-standard\" onClick=\"morphBookingForm($(this), 'new_booking')\">book now</button>"; } ?>
			</section>
			<?php include("components/footer.php"); ?>
		</div>
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
	    });
	    $("#booking-form").validate({
			onfocusout: function(element) { $(element).valid(); }
		});
	</script>
});
</html>