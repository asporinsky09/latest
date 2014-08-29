//TODO: Add jquery validate api

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
		url: 'booking.php',
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
			    advanceForm(fromEl, toEl);
			} else {
				if(data.indexOf("already exists") >= 0) {
					$('#emailerror').css("display", "block");
				}
			}
		}
	});
}

function processCoupon(fromEl, toEl) {
	alert('In');
	$.ajax({
		url: 'booking.php',
		type: 'POST',
		data: {
			function: 'applyCoupon',
			member_id: $('#member_id').val(),
			coupon: $('#coupon').val()
		},
		success: function(data) {
			alert('in success '+data);
			$('#coupon-result').html(data);
			$('#coupon-result').css("display", "block");
			// var price = data;
			//TODO: Remember to handle logging the coupon use at the end of them actually applying it and paying
		}
	});
}

function resizeBookingFormToFieldset(fromEl, toEl) {
	var wrap = toEl.parent();
	var fromOrigHeight = fromEl.height();
	var toHeight = toEl.outerHeight();
	var toLeft = toEl.position().left;
	var toTop = toEl.position().top;

	toEl.css('opacity', '0');
	toEl.show();
	var myTimeline = new TimelineLite({onComplete:function() {
		fromEl.height(fromOrigHeight);
		fromEl.hide();
		fromEl.css('opacity', '1');
		
	}});	
	myTimeline.to(fromEl, .5, {opacity:0,height:toHeight,left:toLeft,top:toTop}, "step1")
	.to(wrap, .5, {height:toHeight,left:toLeft,top:toTop}, "step1")
	.to(toEl, .5, {opacity:1}, "step1");
}

function advanceForm(fromEl, toEl) {
	resizeBookingFormToFieldset(fromEl, toEl);
}


// $(".btn-booking-next").click(function() {
// 	resizeBookingFormToFieldset($(this).parent(), $(this).parent().next());
// });

$(".btn-booking-back").click(function() {
	resizeBookingFormToFieldset($(this).parent(), $(this).parent().prev());
});

$(document).click(function(event) { 
    if(!$(event.target).closest('#booking-form').length) {
        if($('.overlay-wrapper').css("visibility") == "visible") {
            $('.overlay-wrapper').css({visibility:"hidden"});
            $('body').removeClass('blur');
        }
    }        
});

function morphBookingForm(fromEl) {
	var invisEl = $('.overlay-wrapper');
	var toEl = $('#booking-form');
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

