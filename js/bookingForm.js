var bookingFlows = {
	'new_booking': {'flow': ['new-user-form', 'product-select-form', 'address-select-form', 'scheduling-form', 'payment-form', 'booking-success']},
	'new_booking_in': {'flow': ['product-select-form', 'address-select-form', 'scheduling-form', 'payment-form', 'booking-success']},
	'reschedule': {'flow': ['scheduling-form', 'address-select-form', 'booking-success'], 'function':'rescheduleAppointment()'},
	'schedule': {'flow': ['scheduling-form', 'address-select-form', 'booking-success'], 'function':'scheduleAppointment()'},
	'cancel': {'flow': ['cancel-confirm', 'cancel-success'], 'function':'cancelAppointment();'}
}

var lastFlow = '';
var delegateSuccess = false;

function advanceForm(fromEl, direction) {
	direction = (direction >= 0) ? 1 : -1;
	var flow = bookingFlows[lastFlow].flow;
	var func = bookingFlows[lastFlow].function;
	if (func) {
		func = new Function(func);
	}
	var toEl;
	for(var i = 0; i < flow.length; i++) {
		var current = flow[i];
		var next = i + direction;
		if(current.indexOf(fromEl.attr('id')) == 0) {
			toEl = $('#' + flow[next]);
			if (toEl) {
				if (next < flow.length - 2) {  // -2 because of success or feedback form
					toEl.children('.btn-booking-next').html('Next');
				} else if(next == flow.length - 2) {
					if (toEl.children('.btn-booking-next').html() != 'Book') {
						toEl.children('.btn-booking-next').html('Submit');
					}
				} else if(next == flow.length - 1) {
					if(func) {
						func();
						if(!delegateSuccess) {
							return false;
						}
						delegateSuccess = false;
					}
				}
			}
			break;
		}
	}
	if (toEl.children('.btn-booking-back:hidden').length > 0) {
		toEl.children('.btn-booking-back').show();
		toEl.children('.btn-booking-next').removeClass('btn-booking-only');
	}
	resizeBookingFormToFieldset(fromEl, toEl);
}

$(document).click(function(event) { 
    if(!(($(event.target).closest('#booking-form').length) 
    	|| $(event.target).is('[class^="ui-"]'))) {
    	//TODO: That ui- check thing is troublesome... I couldn't find a better way to do this, since that ui element had no parent
        if($('.overlay-wrapper').css("visibility") == "visible") {
        	$('.booking-result').each(function(index) {
        		if($(this).css("display") != "none") {
	            	location.reload();
	        	}
	        });
            $('.overlay-wrapper').css({visibility:"hidden"});
            $('body').removeClass('blur');
            resetBookingForm();  //TODO: Move this so the form stays filled out until I Want it cleared
        }
    }        
});

function attachDatePicker(nearEl) {
	var picker = $('#ui-datepicker-div');
	picker.detach().insertAfter(nearEl);
}

function resetBookingForm() {
	$('#booking-form')[0].reset();
	$('#booking-form fieldset').each(function(index) {
		$(this).hide();
	});
	$('#booking-success:visible').hide();
	removeHidden('coupon_id');
	removeHidden('address_id');
	removeHidden('total_price');
	$('#cart-product').html('');
	$('#cart-price').html('');
	$('#cart-coupon-label').html('');
	$('#cart-coupon').html('');
	$('#cart-coupon-adjust').html('');
	$('#cart-total').html('-');
}

function processAboutMe(form, fromEl, toEl) {
	if($('#emailerror').css("display") != "none") {
		$('#emailerror').css("display", "none");
	}

	var pass = $('#newpw').val();
	var cpass = $('#cnewpw').val();
	if(pass != cpass) {
		$('#pwerror').css("display", "block");
		$("input[type='password']").val('');
		return false;
	}
	if($('#pwerror').css("display") != "none") {
		$('#pwerror').css("display", "none");
	}
	var cryptPass = hex_sha512(pass);
	
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'storeMember',
			fname: $('#fname').val(),
			lname: $('#lname').val(),
			phone: $('#phone').val(),
			email: $('#email').val(),
			cryptPass: cryptPass
		},
		success: function(data) {
			if(data.indexOf("Error") != 0) {
				if($('#emailerror').css("display") != "none") {
					$('#emailerror').css("display", "none");
				}
				var member_id = parseInt(data);
				var member = document.createElement("input");
			    form.appendChild(member);
			    member.name = "member_id";
			    member.id = "member_id";
			    member.type = "hidden";
			    member.value = member_id;
			    advanceForm(fromEl, 1);
			} else {
				if(data.indexOf("already exists") >= 0) {
					$('#emailerror').css("display", "block");
				}
			}
		}
	});
}

function processCoupon() {
	$('#coupon-result').css("display", "none");
	var price = $('#cart-price').html().substr(1);
	if(price) {
		var coupon = $('#coupon').val(); 
		$.ajax({
			url: 'services/booking.php',
			type: 'POST',
			data: {
				function: 'applyCoupon',
				member_id: $('#member_id').val(),
				product_id: $('input[name=product]:checked').val(),
				coupon: coupon,
				price: price
			},
			success: function(data) {
				var result = $.parseJSON(data);
				if (result.error.length > 0) {
					$('#coupon-result').html(result.error);
					$('#coupon-result').css("display", "block");
				} else {
					$('#cart-coupon-label').html('<span class="remove-coupon" onclick="$(\'#cart-coupon\').html(\'\'); $(\'#cart-coupon-adjust\').html(\'\'); $(\'#cart-coupon-label\').html(\'\');">X</span> Coupon');
					$('#cart-coupon').html(coupon);
					$('#cart-coupon-adjust').html('- $' + parseFloat(result.adjust).toFixed(2));
					appendHidden('coupon_id', result.id);
					calculatePrice();
				}
			}
		});
	}
}

function processAddress(fromEl) {
	var address_id = $('#savedAddress').val();
	var address = $('#streetAddress').val();
	var aptnum = $('#aptnum').val();
	var city = $('#city').val();
	var state = $('#state').val();
	var zip = $('#zip').val();
	var instruction = $('#instruction').val();
	if(address.length > 0) {
		if(city.length <=0 || state.length <=0 || zip.length <=0) {
			//TODO: Error handling
			return false;
		}
		$.ajax({
			url: 'services/booking.php',
			type: 'POST',
			data: {
				function: 'addAddress',
				address: address,
				aptnum: aptnum,
				city: city,
				state: state,
				zip: zip,
				instruction: instruction
			},
			success: function(data) {
				if(data) {
					appendHidden('address_id', data);
				}
				advanceForm(fromEl, 1);
			}
		});
	} else if(address_id.length > 0) {
		appendHidden('address_id', address_id);
		advanceForm(fromEl, 1);
	} else {
		return false;
	}
}

function processApptTime(fromEl) {
	advanceForm(fromEl, 1);
}

function completeBooking(fromEl) {
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'doBooking',
			price: $('#total_price').val().substr($('#total_price').val().indexOf('$') + 1),
			product_id: $('input[name=product]:checked').val(),
			product_name: $('input[name=product]').attr("id"),
			coupon_id: $('#coupon_id').val(),
			address: $('#address_id').val(),
			date: $('#date').val(),
			time: $('#time').val(),
			ccnum: $('#ccnum').val(),
			ccexp: $('#expiry').val(),
			ccv: $('#ccv').val()
		},
		success: function(data) {
			alert(data);
			if(data.indexOf("Success") == 0) {
				$('#timedateresult').html($('#date').val() + ' at ' + $('#time').val());
				advanceForm(fromEl, 1);
			} else {
				var result = $.parseJSON(data);
				alert(result.error_detail);
				$('#payment-result').html(result.error_detail);
				$('#payment-result').css("display", "block");
				return false;
			}
		}
	});
	return false;
}

function storeAppointmentInfo(fromEl) {
	var hidden = fromEl.parent().siblings().find('input[type=hidden]');
	var storedId = hidden.val();
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'storeAppointmentId',
			appointmentId: storedId
		},
		success: function(data) {
			if(!data) {
				alert('Something bad happened');
			}
		}
	});
}

function storeOrderInfo(fromEl) {
	var orderId = $('#order_id').val();
	var productId = $('#product_id').val();
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'storeOrderInfo',
			orderId: orderId,
			productId: productId
		},
		success: function(data) {
			if(!data) {
				alert('Something bad happened');
			}
		}
	});
}

function cancelAppointment() {
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'cancelAppointment',
		},
		async: false,
		success: function(data) {
			if(data == 0) {
				delegateSuccess = false;
			}
			delegateSuccess = true;
		}
	});
}

function rescheduleAppointment() {
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'rescheduleAppointment',
			address: $('#address_id').val(),
			date: $('#date').val(),
			time: $('#time').val()
		},
		async: false,
		success: function(data) {
			if(!data) {
				delegateSuccess = false;
			} else {
				$('#timedateresult').html($('#date').val() + ' at ' + $('#time').val());
				delegateSuccess = true;
			}
		}
	});
}

function scheduleAppointment() {
	$.ajax({
		url: 'services/booking.php',
		type: 'POST',
		data: {
			function: 'scheduleAppointment',
			address: $('#address_id').val(),
			date: $('#date').val(),
			time: $('#time').val()
		},
		async: false,
		success: function(data) {
			if(!data) {
				delegateSuccess = false;
			} else {
				$('#timedateresult').html($('#date').val() + ' at ' + $('#time').val());
				delegateSuccess = true;
			}
		}
	});
}

function updatePrice(selectedEl, price) {
	var product = selectedEl.attr("id");
	$('#cart-product').html(product);
	$('#cart-price').html('$' + parseFloat(price).toFixed(2));
	calculatePrice();
}

function calculatePrice() {
	var adjust = 0;
	var price = $('#cart-price').html().substr($('#cart-price').html().indexOf('$') + 1);
	var rawAdjust = $('#cart-coupon-adjust').html();
	if(rawAdjust) {
		adjust = rawAdjust.substr(rawAdjust.indexOf('$') + 1);
	}
	var total = (parseFloat(price) - parseFloat(adjust)).toFixed(2);
	$('#cart-total').html("$"+total);
	appendHidden('total_price', total);
}

function appendHidden(name, data) {
	var hidden;
	if(!$('#booking-form').has('#'+name).length > 0) {
		hidden = $('<input>');
		hidden.attr('type','hidden');
		hidden.attr('id', name);
		hidden.attr('name', name);
	    hidden.appendTo($('#booking-form'));
	}
	$('#'+name).val(data);
}

function removeHidden(name) {
	if($('#booking-form').has('#'+name).length > 0) {
		$('#'+name).remove();
	}
}

function resizeBookingFormToFieldset(fromEl, toEl) {
	var wrap = toEl.parent();
	var fromOrigHeight = fromEl.height();
	var toHeight = toEl.outerHeight();

	toEl.css('opacity', '0');
	toEl.show();
	var myTimeline = new TimelineLite({onComplete:function() {
		fromEl.height(fromOrigHeight);
		fromEl.hide();
		fromEl.css('opacity', '1');
		toEl.css('height', 'auto');
	}});	
	myTimeline.to(fromEl, .5, {opacity:0,height:toHeight}, "step1")
	.to(wrap, .5, {height:toHeight}, "step1")
	.to(toEl, .5, {opacity:1}, "step1");
}

function morphBookingForm(fromEl, flowKey, loggedIn) {
	var invisEl = $('.overlay-wrapper');
	var toEl = $('#booking-form');

	var flow = bookingFlows[flowKey].flow;
	if(flow) {
		var initialPage = flow[0];
		var targetPage = initialPage;
		var page = $('#' + targetPage);
		if (page.length == 0) {
			return false;
		}
		if(lastFlow != flowKey) {
			resetBookingForm();
			page.children('.btn-booking-back').hide();
			page.children('.btn-booking-next').addClass('btn-booking-only');
		}
		lastFlow = flowKey;
		page.show();

		var myTimeline = new TimelineLite({onComplete:function() {
			clone.remove();
			toEl.css('pointer-events','auto');
			toEl.parent().css('pointer-events','auto');
		}});
		toEl.outerHeight(toEl.children(':visible:first').outerHeight());

		var fromLeft, fromTop, toLeft, toTop, toHeight, toWidth;
		var fromPos =  fromEl.offset();
		fromLeft = fromPos.left;
		fromTop = fromPos.top;
		toLeft = toEl.offset().left;
		toTop = toEl.offset().top;
		toWidth = toEl.outerWidth();
		toHeight = toEl.outerHeight();

		var clone = toEl.clone();
		clone.css({position:"fixed",width:fromEl.outerWidth(),height:fromEl.outerHeight(),visibility:"visible",overflow:"hidden"});
		clone.offset({left:fromLeft,top:fromTop});
		clone.insertBefore(toEl);

		if (invisEl.parent().is('body')) {
			 // Remove from blur 
			invisEl.insertAfter($('body'));	
		}
		
		$('body').addClass('blur');
		myTimeline.to(clone, .5, {left:toLeft,top:toTop,margin:0,width:toWidth,height:toHeight,backgroundColor:"rgba(252, 220, 226, .6)"})
		.to(clone, 0, {visibility:"hidden",opacity:0})
		.to(invisEl, 0, {visibility:"visible"})
		return true;
	}
	return false;
}