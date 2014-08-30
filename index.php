<?php
include_once 'inc/functions.php';
include("components/pagebegin.php"); ?>
	<body>
		<div class="overlay">
			<header class="clear">
				<a href="index.php"><h1 class="logo">blohaute</h1></a>
				<?php include("components/userbar.php"); ?>
			</header>
			<?php include("components/nav.php"); ?>
			<section id="content-main">
				<h2>this is blohaute</h2>
				<p>its a mobile on demand styling app and you should sign up so we can get rich and buy a yacht. sounds good.</p>
				<button class=" btn-book btn-standard" onClick="morphBookingForm($(this), $('.overlay-wrapper'), $('#booking-form'))">book now</button>
			</section>
			<?php include("components/footer.php"); ?>
		</div>
	</body>
	<?php include("booking-form.php"); ?>
	<script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/jquery-1.11.1.min.js"></script> 
    <script type="text/JavaScript" src="js/jquery-ui.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="js/jquery-ui.min.css">
    <script src="js/TweenLite.min.js"></script>
	<script src="js/TimelineLite.min.js"></script>
	<script src="js/plugins/CSSPlugin.min.js"></script>
    <script type="text/javascript" src="js/bookingForm.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <script>
	    $(function() {
	    	$( "#date" ).datepicker({minDate: 0});
	    });
	</script>
</html>