{{--
	Timed lead-capture popup.
	First appearance after 10s; if dismissed, it returns after 30s.
	Once submitted (or dismissed repeatedly) it stays away — see app.js.
--}}
<x-lead-modal
	id="lead-popup"
	:action="route('enquiry.store')"
	source="popup"
	title="Looking for a property in Navi Mumbai?"
	text="Share your details and our team will send you matching options with prices."
	button-label="Get Property Options"
	data-lead-popup
	data-first-delay="10000"
	data-repeat-delay="30000" />
