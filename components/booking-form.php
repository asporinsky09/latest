<?php
	include_once 'inc/functions.php';
	include_once 'inc/dbConnect.php';
	include_once 'inc/Session.php';
	include_once 'inc/Member.php';

	$logged_in = login_check($db);
?>

<div class="overlay-wrapper">
	<div class="vertical-align-wrapper center modal-wrapper">
		<form id="booking-form" class="vertical-center modal-content">
			<form>
		<?php if(!$logged_in) { ?>
			<fieldset id="new-user-form">
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
				<div class="grid clear"> 
					<div class="col-1-2"><input type="password" name="newpw" id="newpw" placeholder="password" required minlength="8"></div>
					<div class="col-1-2"><input type="password" name="cnewpw" id="cnewpw" placeholder="confirm" required minlength="8"></div>
				</div>
				<span id="pwerror" class="response clear">Passwords do not match</span>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="processAboutMe(this.form, $(this).parent(), $(this).parent().next())">Next</button>
			</fieldset>
		<?php } ?>
			<fieldset id="product-select-form">
				<div id="booking-product">
					<h2>What can we do for you?</h2>
					<input type="radio" name="product" id="blowout" value="Blowout" required onclick="updatePrice($(this));">
					<label for="blowout">Blowout</label>
					<input type="radio" name="product" id="braid" value="Braid" onclick="updatePrice($(this));">
					<label for="braid">Braid</label>
					<input type="radio" name="product" id="up-do" value="Up-Do" onclick="updatePrice($(this));">
					<label for="up-do">Up-Do</label>
					<input type="radio" name="product" id="package" value="package">
					<label for="package">Package</label>
				</div>
				<div id="booking-reciept" class="grid clear">
					<div id="booking-price" class="grid clear">
						<table id="price-detail" class="col-1">
							<tr>
								<td id="cart-product" colspan="2"></td>
								<td id="cart-price"></td>
							</tr>
							<tr>
								<td id="cart-coupon-label"></td>
								<Td id="cart-coupon"></td>
								<td id="cart-coupon-adjust"></td>
							</tr>
							<tfoot>
								<tr>
									<td if="cart-total-label" colspan="2">Total Price:</td>
									<td id="cart-total">-</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<span class="form-detail">Do you have a coupon?</span>
					<div class="inside-button-wrapper" id="coupon-wrapper">
						<input type="text" name="coupon" id="coupon" placeholder="coupon code" maxlength="18"class="inside-button-outer">
						<button type="button" class="inside-button-button" id="coupon-apply" onclick="processCoupon();">Apply</button>
					</div>
				</div>
				<div class="grid clear">
					<span class="response col-1" id="coupon-result"></span>
				</div>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="advanceForm($(this).parent(), 1);">Next</button>
			</fieldset>
			<fieldset id="address-select-form">
				<h2>Where do we send our stylist?</h2>
				<?php $addrRequired = true;
				if($logged_in) { 
					$member_id = $_SESSION['member_id'];
					$options = getMemberAddressesAsOptions($db, $member_id);
					if(!empty($options)) {
						$addrRequired = false;
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
					<div class="col-4-5"><input type="text" minlength="3" name="streetAddress" id="streetAddress" <?php $addrRequired ? "required " : "" ?> placeholder="street address"></div>
					<div class="col-1-5"><input type="text" name="aptnum" id="aptnum" placeholder="apt #" class="col-1-5"></div>
				</div>
				<div class="grid clear">
					<div class="col-3-5"><input type="text" name="city" id="city" <?php $addrRequired ? "required " : "" ?> placeholder="city" minlength ="2"></div>
					<div class="col-15-pct"><input type="text" name="state" id="state" placeholder="st" <?php $addrRequired ? "required " : "" ?> minlength="2" maxlength="2"></div>
					<div class="col-1-4"><input type="text" name="zip" id="zip" placeholder="zip" minlength ="5" <?php $addrRequired ? "required " : "" ?>></div>
				</div>
				<div class="grid clear">
					<div class="col-1"><input type="text" name="instruction" id="instructions" placeholder="special instructions" class="col-1"></div>
				</div>
				<button type="button" class="btn-booking-back" onclick="advanceForm($(this).parent(), -1);">Back</button>
				<button type="button" class="btn-booking-next" onclick="processAddress($(this).parent());">Next</button>
			</fieldset>
			<fieldset id="scheduling-form">
				<h2>When should we arrive?</h2>
				<div class="grid clear">
					<span class="fa fa-calendar fa-3x col-1-3"></span>
					<div class="col-2-3"><input type="text" name="date" id="date" placeholder="mm/dd/yyyy" required onclick="attachDatePicker($(this));"></div>
				</div>
				<div class="grid clear">
					<span class="fa fa-clock-o fa-3x col-1-3"></span>
					<div class="col-2-3"><input type="text" name="time" id="time" required placeholder="hh:mm AM"></div>
				</div>
				<span class="response">result</span>
				<button type="button" class="btn-booking-back" onclick="advanceForm($(this).parent(), -1);">Back</button>
				<button type="button" class="btn-booking-next" onclick="processApptTime($(this).parent());">Next</button>
			</fieldset>
			<fieldset id="payment-form">
				<h2>Credit Card Info</h2>
				<div class="grid clear">
					<div class="col-1"><input type="text" name="ccnum" id="ccnum" placeholder="credit card #"></div>
				</div>
				<span>Visa/MC/Discover</span>
				<div class="grid clear">
					<div class="col-4-5"><input type="text" name="expiry" id="expiry" placeholder="Expiration: MM/YY"></div>
					<div class="col-1-5"><input type="text" name="ccv" id="ccv" placeholder="CCV"></div>
				</div>
				<div class="grid clear">
					<span class="response col-1" id="payment-result"></span>
				</div>
				<button type="button" class="btn-booking-back" onclick="advanceForm($(this).parent(), -1);">Back</button>
				<button type="button" class="btn-booking-next" onclick="completeBooking($(this).parent());">Book</button>
			</fieldset>
			<fieldset id="cancel-confirm">
				<h2>Are you sure you want to cancel</h2>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="advanceForm($(this).parent(), 1);">Cancel</button>
			</fieldset>
			<div class="booking-result" id="booking-success">
				<section>
					<h2>Thanks for using Blohaute!</h2>
					<h3>See you on <span id="timedateresult"></span></h3>
				</section>
				<section>
					<h2>be gorgeous</h2>
				</section>
			</div>
			<div class="booking-result" id="cancel-success">
				<section>
					<h2>Thanks for using Blohaute!</h2>
					<h3>Your appointment has been cancelled</h3>
				</section>
				<section>
					<h2>be gorgeous</h2>
				</section>
			</div>
		</form>
	</div>
</div>