<?php 
include_once 'inc/functions.php';
include_once 'inc/dbConnect.php';
include_once 'inc/Session.php';
include_once 'inc/Appointment.php';

?>
<section class="userbar">
<?php if (! $logged_in) { ?>
	<div class="login vertical-align-wrapper">
		<form name="sign-in" action="inc/process_login.php" method="POST" class="login vertical-center">
			<input type="email" name="email" placeholder="Email Address">
			<input type="password" name="password" placeholder="Password:" class="inside-button-outer">
			<button type="submit" class="btn-login" onclick="secureSend(this.form, this.form.password); this.form.submit();">Sign in</button>
		</form>
	</div>
	<?php } else { 
		$fname = $_SESSION['member']['fname'];
		$lname = $_SESSION['member']['lname'];
		$email = $_SESSION['member']['email'];
		$phone = $_SESSION['member']['phone'];
		$appointments = getAppointmentsForMember($db, $_SESSION['member_id']);
		$unscheduled = getUnscheduledForMember($db, $_SESSION['member_id']);
		$total_unsched_remaining = 0;
		foreach ($unscheduled as $future) {
			$total_unsched_remaining += $future['max_uses'] - $future['used'];
		}
	?>
	<div class="clear" id="userbar-user">
		<div class="vertical-align-wrapper" id="user-button-wrapper">
			<button class="vertical-center user-button" onclick="$('#user-details').toggle(); $('.user-option-box:visible').not('#user-details').hide();"><span class="fa fa-user fa-2x"></span></button>
		</div>
		<div class="vertical-align-wrapper" id="userbar-header">
			<div class="vertical-center">
				<div class="clear">
					<span class="userbar-header-info" id="greeting" onclick="$('#user-details').toggle(); $('.user-option-box:visible').not('#user-details').hide();">welcome back gorgeous</span>
					<button type="button" class="userbar-header-info" id="userbar-scheduled-header" <?php echo (count($appointments) > 0) ? "onclick=\"$('#scheduled-details').toggle(); $('.user-option-box:visible').not('#scheduled-details').hide();\"" : "" ?>>
						[<?php echo count($appointments) ?>] scheduled</button>
					<button type="button" class="userbar-header-info" id="userbar-services-header" onclick="$('#services-details').toggle(); $('.user-option-box:visible').not('#services-details').hide();">
						[<?php echo $total_unsched_remaining ?>] available</button>
				</div>
			</div>
		</div>
		<section class="user-option-box" id="user-details">
			<div class="user-option-section">
				<section class="options options-fixed clear">
					<div class="col-auto col-no-outer-pad">
						<span><?php echo $fname.' '.$lname ?></span>
						<span><?php echo $email ?></span>
						<span><?php echo $phone ?></span>
						<div class="col-1" id="change-password">
							<input type="password" name="user-pass" id="user-pass" placeholder="Current Password">
							<input type="password" name="user-npass" id="user-npass" placeholder="New Password">
							<input type="password" name="user-cpass" id="user-cpass" placeholder="Confirm Password">
						</div>
						<span class="span-btn" id="change-password-button" onclick="changePassword();">Change password</span>
					</div>
					<div class="col-auto col-last col-no-outer-pad">
						<button class="option-button" onclick="toggleOptionEdit($(this));"><span class="fa fa-pencil-square-o fa-3x"></span></button>
					</div>
				</section>
				<section class="options options-edit clear">
					<div class="col-auto col-no-outer-pad">
						<input type="text" name="user-fname" id="user-fname" value="<?php echo $fname ?>">
						<input type="text" name="user-lname" id="user-lname" value="<?php echo $lname ?>">
						<input type="email" name="user-email" id="user-email" value="<?php echo $email ?>">
						<input type="tel" name="user-phone" id="user-phone" value="<?php echo $phone ?>">
					</div>
					<div class="col-auto col-last col-no-outer-pad">
						<button class="option-button" onclick="toggleOptionEdit($(this));"><span class="fa fa-check-square-o fa-3x"></span></button>
					</div>
				</section>
			</div>
			<div class="user-option-section">
				<a href="inc/logout.php"><span class="span-btn">Sign Out</span></a>
			</div>
		</section>
		<section class="user-option-box" id="scheduled-details">
			<ul>
				<?php 
				foreach ($appointments as $appointment) {
				?>
					<div class="user-option-section">
						<section class="options options-fixed clear">
							<div class="col-auto col-no-outer-pad">
								<?php echo formatAppointment($appointment); ?>
							</div>
							<div class="col-auto col-last col-no-outer-pad">
								<!-- TODO: Open schedule and address selection from booking when the button is clicked -->
								<button class="option-button" onclick="storeAppointmentInfo($(this)); morphBookingForm($(this), 'reschedule')"><span class="fa fa-pencil-square-o fa-3x"></span></button>
								<button class="option-button" onclick="storeAppointmentInfo($(this)); morphBookingForm($(this), 'cancel')"><span class="fa fa-times fa-3x"></span></button>
							</div>
						</section>
					</div>
				<?php } ?>
			</ul>
		</section>
		<section class="user-option-box" id="services-details">
			<ul>
				<?php 
				foreach ($unscheduled as $future) {
				?>
					<div class="user-option-section">
						<section class="options options-fixed clear">
							<div class="col-auto col-no-outer-pad">
								<li class="unscheduled-entry">
									<input type="hidden" id="order_id" value="<?php echo $future['order_id'] ?>">
									<input type="hidden" id="product_id" value="<?php echo $future['product_id'] ?>">
									<h3>Booked on <?php echo $future['submitted'] ?></h3>
									<span><?php echo $future['product_name'] ?></span>
									<span><?php echo $future['max_uses'] - $future['used'] ?> Blowouts Remaining</span>
								</li>
							</div>
							<div class="col-auto col-last col-no-outer-pad">
								<!-- TODO: Open schedule and address selection from booking when the button is clicked -->
								<button class="option-button" onclick="storeOrderInfo($(this)); morphBookingForm($(this), 'schedule')"><span class="fa fa-calendar fa-3x"></span></button>
							</div>
						</section>
					</div>
				<?php } ?>
			</ul>
		</section>
	</div>	
<?php } ?>
</section>