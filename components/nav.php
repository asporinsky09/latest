<nav>
			<ul>
				<div class="vertical-align-wrapper">
					<div class="vertical-center grid">
						<a href="styles.php"><li>styles</li></a>
						<a href="social.php"><li>social</li></a>
						<?php if($logged_in) { echo "<a onClick=\"morphBookingForm($(this), 'new_booking_in')\"><li>booking</li></a>"; }
						 else { echo "<a onClick=\"morphBookingForm($(this), 'new_booking')\"><li>booking</li></a>"; } ?>
						<a href="press.php"><li>press</li></a>
					</div>
				</div>
			</ul>
		</nav>
