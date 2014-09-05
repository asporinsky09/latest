<?php
	include_once 'inc/functions.php';
	include_once 'inc/dbConnect.php';
	include_once 'inc/Session.php';
	include_once 'inc/Member.php';
	include_once 'inc/Product.php';

	$logged_in = login_check($db);
	$products = getProducts($db);
	$packages = getPackages($db);
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
				<span class="response"></span>
				<div class="grid clear">
					<div class="col-1"><input type="tel" name="phone" id="phone" placeholder="phone number" minlength="10" maxlength="10" required></div>
				</div>
				<span class="response"></span>
				<div class="grid clear">
					<div class="col-1"><input type="email" name="email" id="email" placeholder="email address" required></div>
				</div>
				<span class="response"></span>
				<span id="emailerror" class="response clear">Email address already registered, please log in at top of page</span>
				<div class="grid clear"> 
					<div class="col-1-2"><input type="password" name="newpw" id="newpw" placeholder="password" required minlength="8"></div>
					<div class="col-1-2"><input type="password" name="cnewpw" id="cnewpw" placeholder="confirm" required minlength="8"></div>
				</div>
				<span class="response"></span>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="processAboutMe($(this).parent())">Next</button>
			</fieldset>
		<?php } ?>
			<fieldset id="product-select-form">
				<div id="booking-product">
					<h2>What can we do for you?</h2>
					<div id="regular-products">
						<?php 
						foreach ($products as $product) {
						?>
							<input type="radio" name="product" id="<?php echo $product['product'] ?>" value="<?php echo $product['product_id'] ?>" required onclick="updatePrice($(this), <?php echo $product['price'] ?>);">
							<label for="<?php echo $product['product'] ?>"><?php echo $product['product'] ?></label>
						<?php } ?>
						<button type="button" id="packages-button" onclick="$(this).parent().siblings().show(); $(this).parent().hide();">Packages</button>
					</div>
					<div class="package-products">
						<?php 
						foreach ($packages as $package) {
						?>
							<input type="radio" name="product" id="<?php echo $package['product'] ?>" value="<?php echo $package['product_id'] ?>" required onclick="updatePrice($(this), <?php echo $package['price'] ?>);">
							<label for="<?php echo $package['product'] ?>"><?php echo $package['product'] ?></label>
						<?php } ?>
						<button type="button" id="packages-button" onclick="$(this).parent().siblings().show(); $(this).parent().hide();">Back</button>
					</div>
				</div>
				<span class="response"></span>
				<div id="booking-reciept" class="grid clear">
					<div id="booking-price" class="grid clear">
						<table id="price-detail" class="col-1">
							<tr>
								<td id="cart-product" colspan="2"></td>
								<td id="cart-price"></td>
							</tr>
							<tr>
								<td id="cart-coupon-label"></td>
								<td id="cart-coupon"></td>
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
					<span class="form-detail">Do you have a coupon or voucher?</span>
					<div class="inside-button-wrapper" id="coupon-wrapper">
						<input type="text" name="coupon" id="coupon" placeholder="coupon code" maxlength="18" class="inside-button-outer">
						<button type="button" class="inside-button-button" id="coupon-apply" onclick="processCoupon();">Apply</button>
					</div>
				</div>
				<div class="grid clear">
					<span class="response col-1" id="coupon-result"></span>
				</div>
				<button type="button" class="btn-booking-next btn-booking-only" onclick="validateProduct($(this).parent());">Next</button>
			</fieldset>
			<fieldset id="address-select-form">
				<h2>Where should we send our stylist?</h2>
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
				<h2>or use a new address:</h2>
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
				<span class="response"></span>
				<button type="button" class="btn-booking-back" onclick="advanceForm($(this).parent(), -1);">Back</button>
				<button type="button" class="btn-booking-next" onclick="processAddress($(this).parent());">Next</button>
			</fieldset>
			<fieldset id="scheduling-form">
				<h2>When should we arrive?</h2>
				<div class="grid clear">
					<span class="fa fa-calendar fa-3x col-1-3"></span>
					<div class="col-2-3"><input type="text" name="date" id="date" placeholder="mm/dd/yyyy" required onfocus="attachDatePicker($(this));" onclick="attachDatePicker($(this));"></div>
				</div>
				<div class="grid clear">
					<span class="fa fa-clock-o fa-3x col-1-3"></span>
					<div class="col-2-3"><input type="text" name="time" id="time" required placeholder="hh:mm am/pm"></div>
				</div>
				<span class="response"></span>
				<span class="form-detail">Mon - Sun | 6am - 9pm</span>
				<button type="button" class="btn-booking-back" onclick="advanceForm($(this).parent(), -1);">Back</button>
				<button type="button" class="btn-booking-next" onclick="processApptTime($(this).parent());">Next</button>
			</fieldset>
			<fieldset id="payment-form">
				<h2>Credit Card Info</h2>
				<div class="grid clear">
					<div class="col-1"><input type="text" name="ccnum" id="ccnum" required placeholder="credit card #"></div>
				</div>
				<span class="response"></span>
				<div class="grid clear">
					<div class="col-4-5"><input type="text" name="expiry" id="expiry" required placeholder="Expiration: mm/yy"></div>
					<div class="col-1-5"><input type="text" name="ccv" id="ccv" required placeholder="CVV"></div>
				</div>
				<span class="response"></span>
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
			<div class="content-section-modal" id="events-success">
				<section>
					<h2>Thanks for using Blohaute!</h2>
					<h3>You will hear from us within 24 hours</h3>
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
			<div class="content-section-modal" id="styles-modal">
				<section>
					<h4>you caught us with our hair down</h4>
					<h4>this page will be ready soon</h4>
					<h3>until then check our social media pages for style ideas</h3>
				</section>
				<section>
					<a href="http://www.facebook.com/blohaute" target="_blank"><img src="img/fb_white.png"></a>
					<a href="http://www.instagram.com/blohaute" target="_blank"><img src="img/ig_white.png"></a>
					<a href="http://www.pinterest.com/blohaute" target="_blank"><img src="img/pin_white.png"></a>
				</section>
			</div>
			<fieldset class="content-section-modal" id="events-modal">
				<h2>Event Description</h2>
				<div class="grid clear">
					<div class="col-1-2"><input type="text" name="event_req_name" id="event_req_name" required placeholder="Name"></div>
					<div class="col-1-2"><input type="email" name="event_req_email" id="event_req_email" required placeholder="Email"></div>
				</div>
				<span class="response"></span>
				<div class="grid clear">
					<div class="col-1-2"><input type="tel" name="event_req_phone" id="event_req_phone" required placeholder="Phone"></div>
					<div class="col-1-2"><input type="text" name="event_req_date" id="event_req_date" required placeholder="Event date" onfocus="attachDatePicker($(this));" onclick="attachDatePicker($(this));"></div>
				</div>
				<div class="grid clear">
					<div class="col-1"><textarea type="text" name="event_details" id="event_details" rows="5" required placeholder="Event details"></textarea></div>
				</div>
				<span class="response"></span>
				<button type="button" class="btn-booking-next" onclick="advanceForm($(this).parent(), 1);">Submit</button>
			</fieldset>
			<div class="content-section-modal" id="contact-modal">
				<section>
					<h4>For all business and media inquiries please contact <br><a href="mailto:amanda@blohaute.com">Amanda Soltwisch</a></h4>
				</section>
				<section>
					<h3>Amanda Soltwisch</h3>
					<h3>Founder | Master Stylist</h3>
					<h3>e: amanda@blohaute.com</h3>
					<h3>t: (312) 961-6190</h3>
				</section>
				<section>
					<h2>be gorgeous</h2>
				</section>
			</div>
		</form>
	</div>
</div>