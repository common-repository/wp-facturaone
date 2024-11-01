$.ajax({
   beforeSend: function (qXHR, settings) {
	$('#loading').fadeIn();
   },
   complete: function () {
	$('#loading').fadeOut();
   },
   type : "post",
   url : wp_ajax_tets_vars.ajax_url,
   data : {
       action: "notify_button_click", 
       message : "Button has been clicked!"
   },
   success: function(response) {
       $('#txtMessage').text(response);
   }
})