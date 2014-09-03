<nav>
			<ul>
				<div class="vertical-align-wrapper">
					<div class="vertical-center grid">
						<a onclick="morphBookingForm($(this), 'styles')"><li>styles</li></a>
						<?php if($logged_in) { echo "<a onClick=\"morphBookingForm($(this), 'new_booking_in')\"><li>booking</li></a>"; }
						 else { echo "<a onClick=\"morphBookingForm($(this), 'new_booking')\"><li>booking</li></a>"; } ?>
						<a onclick="morphBookingForm($(this), 'events')"><li>events</li></a>
						<a onclick="morphBookingForm($(this), 'contact')"><li>contact</li></a>
					</div>
				</div>
			</ul>
		</nav>