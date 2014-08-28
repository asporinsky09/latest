<?php
include_once 'inc/functions.php';

include("components/pagebegin.php"); ?>
	<body>
		<header class="clear">
			<a href="index.php"><h1 class="logo">blohaute</h1></a>
			<?php include("components/userbar.php"); ?>
		</header>
		<?php include("components/nav.php"); ?>
		<section class="content">
			<form id="booking-form" action="booking.php">
				<fieldset id="choose-style">
					<h2>Choose a style</h2>
					<input type="radio" name="product" id="blowout" value="blowout">
					<label for="blowout">Blowout</label>
					<input type="radio" name="product" id="braid" value="braid">
					<label for="braid">Braid</label>
					<input type="radio" name="product" id="up-do" value="up-do">
					<label for="up-do">Up-Do</label>
					<input type="radio" name="product" id="package" value="package">
					<label for="package">Package</label>
					<div class="grid clear">
						<span>Do you have a coupon?</span>
						<div class="inside-button-wrapper" id="coupon-wrapper">
							<input type="text" name="coupon" id="coupon" placeholder="coupon code" maxlength="18"class="inside-button-outer">
							<button type="button" class="inside-button-button" id="coupon-apply">Apply</button>
						</div>
					</div>
					<span class="form-sub-result">result</span>
					<button type="button" class="btn-booking-next btn-booking-only">Next</button>
				</fieldset>
				<fieldset>
					<h2>When should we arrive?</h2>
					<div class="grid clear">
						<span class="fa fa-calendar fa-3x col-1-3"></span>
						<div class="col-2-3"><input type="text" name="date" id="date" placeholder="mm/dd/yyyy"></div>
					</div>
					<div class="grid clear">
						<span class="fa fa-clock-o fa-3x col-1-3"></span>
						<div class="col-2-3"><input type="text" name="time" id="time" placeholder="hh:mm AM"></div>
					</div>
					<span class="form-sub-result">result</span>
						<button type="button" class="btn-booking-back">Back</button>
						<button type="button" class="btn-booking-next">Locaton</button>
				</fieldset>
				<fieldset>
					<h2>Where do we send our stylist?</h2>
					<div class="grid clear">
						<div class="col-4-5"><input type="text" name="streetAddress" id="streetAddress" placeholder="street address"></div>
						<div class="col-1-5"><input type="text" name="aptnum" id="aptnum" placeholder="apt #" class="col-1-5"></div>
					</div>
					<div class="grid clear">
						<div class="col-3-5"><input type="text" name="city" id="city" placeholder="city"></div>
						<div class="col-1-5"><input type="text" name="state" id="state" placeholder="ST" length="2"></div>
						<div class="col-1-5"><input type="text" name="zip" id="zip" placeholder="zip"></div>
					</div>
					<div class="grid clear">
						<div class="col-1"><input type="text" name="instruction" id="instructions" placeholder="special instructions" class="col-1"></div>
					</div>
					<button type="button" class="btn-booking-back">Back</button>
					<button type="button" class="btn-booking-next">About You</button>
				</fieldset>
				<fieldset>
					<h2>Who are you</h2>
					<div class="grid clear">
						<div class="col-2-5"><input type="text" name="fname" id="fname" placeholder="first"></div>
						<div class="col-3-5"><input type="text" name="lname" id="lname" placeholder="last"></div>
					</div>
					<div class="grid clear">
						<div class="col-1"><input type="text" name="phone" id="phone" placeholder="phone number"></div>
					</div>
					<span>Optional: Create a password so you can skip this next time</span>
					<div class="grid clear">
						<div class="col-1-2"><input type="text" name="newpw" id="newpw" placeholder="password"></div>
						<div class="col-1-2"><input type="text" name="cnewpw" id="cnewpw" placeholder="confirm"></div>
					</div>
					<button type="button" class="btn-booking-back">Back</button>
					<button type="button" class="btn-booking-next">Payment</button>
				</fieldset>
				<fieldset>
					<h2>We'll need a credit card to schedule your appointment</h2>
					<div class="grid clear">
						<div class="col-1"><input type="text" name="ccnum" id="ccnum" placeholder="credit card #"></div>
					</div>
					<span>Visa/MC/Discover</span>
					<div class="grid clear">
						<div class="col-4-5"><input type="text" name="expiry" id="expiry" placeholder="Expiration: MM / YY"></div>
						<div class="col-1-5"><input type="text" name="cvv" id="cvv" placeholder="CVV"></div>
					</div>
					<div class="grid clear"><button type="submit" class="btn-booking-only">Book</button></div>
				</fieldset>
			</form>
		</section>
		<?php include("components/footer.php"); ?>
	</body>
	<script type="text/JavaScript" src="js/jquery-1.11.1.min.js"></script> 
    <script type="text/JavaScript" src="js/bookingForm.js"></script> 
</html>