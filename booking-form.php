<?php
	include_once 'inc/functions.php';
	include_once 'inc/dbConnect.php';
	include_once 'inc/Session.php';
	include_once 'inc/Member.php';

	$logged_in = login_check($db);
?>

<div class="overlay-wrapper">
	<div class="vertical-align-wrapper center modal-wrapper">
		<form id="booking-form" method="POST" class="vertical-center modal-content">
		<?php if(!$logged_in) { ?>
			<fieldset>
				<h2>Tell us about you</h2>
				<div class="grid clear">
					<div class="col-2-5"><input type="text" name="fname" id="fname" placeholder="first" required></div>
					<div class="col-3-5"><input type="text" name="lname" id="lname" placeholder="last" required></div>
				</div>
				<div class="grid clear">
					<div class="col-1"><input type="tel" name="phone" id="phone" placeholder="phone number" required></div>
				</div>
				<div class="grid clear">
					<div class="col-1"><input type="email" name="email" id="email" placeholder="email address" required></div>
				</div>
				<span id="emailerror" class="response clear">Email address already registered, please log in at top of page</span>
				<span>Optional: Create a password so you can skip this next time</span>
				<div class="grid clear"> 
					<div class="col-1-2"><input type="password" name="newpw" id="newpw" placeholder="password" required minlength="8"></div>
					<div class="col-1-2"><input type="password" name="cnewpw" id="cnewpw" placeholder="confirm" required minlength="8"></div>
				</div>
				<span id="pwerror" class="response clear">Passwords do not match</span>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="processAboutMe(this.form, $(this).parent(), $(this).parent().next())">Next</button>
			</fieldset>
		<?php } ?>
			<fieldset>
				<h2>What can we do for you?</h2>
				<input type="radio" name="product" id="blowout" value="blowout" required>
				<label for="blowout">Blowout</label>
				<input type="radio" name="product" id="braid" value="braid">
				<label for="braid">Braid</label>
				<input type="radio" name="product" id="up-do" value="up-do">
				<label for="up-do">Up-Do</label>
				<input type="radio" name="product" id="package" value="package">
				<label for="package">Package</label>
				<div class="grid clear">
					<span class="form-detail">Do you have a coupon?</span>
					<div class="inside-button-wrapper" id="coupon-wrapper">
						<input type="text" name="coupon" id="coupon" placeholder="coupon code" maxlength="18"class="inside-button-outer">
						<button type="button" class="inside-button-button" id="coupon-apply" onclick="processCoupon();">Apply</button>
					</div>
				</div>
				<span class="response" id="coupon-result"></span>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="advanceForm($(this).parent(), $(this).parent().next());">Where</button>
			</fieldset>
			<fieldset>
				<h2>Where do we send our stylist?</h2>
				<?php if($logged_in) { 
					$member_id = $_SESSION['member_id'];
					$options = getMemberAddressesAsOptions($db, $member_id);
					if(!empty($options)) {
				?>
						<div class="grid clear">
							<div class="col-1">
								<select id="savedAddress" name="savedAddress">
									<?php echo $options; ?>
								</select>
							</div>
						</div>
				<h2>or use a different address:</h2>
				<?php } 
				} ?>
				<div class="grid clear">
					<div class="col-4-5"><input type="text" name="streetAddress" id="streetAddress" placeholder="street address"></div>
					<div class="col-1-5"><input type="text" name="aptnum" id="aptnum" placeholder="apt #" class="col-1-5"></div>
				</div>
				<div class="grid clear">
					<div class="col-3-5"><input type="text" name="city" id="city" placeholder="city"></div>
					<div class="col-15-pct"><input type="text" name="state" id="state" placeholder="st" length="2"></div>
					<div class="col-1-4"><input type="text" name="zip" id="zip" placeholder="zip"></div>
				</div>
				<div class="grid clear">
					<div class="col-1"><input type="text" name="instruction" id="instructions" placeholder="special instructions" class="col-1"></div>
				</div>
				<button type="button" class="btn-booking-back">Back</button>
				<button type="button" class="btn-booking-next" onclick="processAddress($(this).parent(), $(this).parent().next());">When</button>
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
				<span class="response">result</span>
				<button type="button" class="btn-booking-back">Back</button>
				<button type="button" class="btn-booking-next" onclick="processApptTime($(this).parent(), $(this).parent().next());">Payment</button>
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
				<button type="button" class="btn-booking-back">Back</button>
				<button type="submit" class="btn-booking-next" onclick="secureSend(this.form, this.form.newpw); this.form.submit();">Book</button>
			</fieldset>
		</form>
	</div>
</div>