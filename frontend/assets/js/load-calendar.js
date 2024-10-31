jQuery(document).ready(function() {
  var theme = jQuery("#calendar-wrap").data("theme");
  data = frontend_ajax_object.calendar_data;

  data = JSON.parse(data);
  MyEventCalendar("myeventcalendar", {
    theme: theme,
    showAddEventButton: true,
    sources: [frontend_ajax_object.ajaxurl],
    //source: data,
    target: frontend_ajax_object.ajaxurl,
    isAdmin: true,
    height: 700,
    isWordpress: true
  });
});
