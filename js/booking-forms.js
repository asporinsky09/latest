var current_page, next_page, previous_page;
$(".btn-booking-next").click(function() {
	current_page = $(this).parent();
	next_page = $(this).parent().next();

	current_page.hide();
	next_page.show();
});

$(".btn-booking-back").click(function() {
	current_page = $(this).parent();
	previous_page = $(this).parent().prev();

	current_page.hide();
	previous_page.show();
});