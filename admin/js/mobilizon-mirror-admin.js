(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

// Still not the cleanest, most modular code: ask for help to a proper JavaScript Developer
jQuery(document).ready( function($) {
	var url = document.getElementById("mobilizon-instance_url");
	if ( url == null ) {
		return;
	}
	var urlIcon = document.getElementById("mobilizon-instance_url-feedback-icon");
	var groups =  document.getElementsByClassName("mobilizon-group_name");

	function checkInstanceField() {
		if ( url.value.split(".").length < 2)  {
			resetInstanceField();
			return;
		}
		if ( url.value.length < 5) {
			resetInstanceField();
			return;
		}
		if (url.value.split("/@").length == 2) {
			let actor = url.value.split("/@");
			url.value = actor[0];
			document.getElementsByClassName("mobilizon-group-item")[0].children[0].value = actor[1];
		}
		let data = {
			action: 'is_mobilizon_instance',
			instance_url: url.value
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response) {
				url.classList.add("input-valid");
				url.classList.remove("input-warning");
				urlIcon.classList.add("icon-valid");
				urlIcon.classList.remove("icon-warning");
				checkGroupFields();
			} else {
				url.classList.add("input-warning");
				url.classList.remove("input-valid");
				urlIcon.classList.add("icon-warning");
				urlIcon.classList.remove("icon-valid");
			}
		},"json");
	}

	function checkGroupField(event) {
		if ( 'target' in event ) {
			var group = $(event.target)[0];
		}
		else {
			var group = event;
		}
		group.value = group.value.toLowerCase().replace('-', '');
		let groupIcon = group.nextElementSibling;
		if ( group.value == '' ) {
			return;
		}
		let data = {
			action: 'is_mobilizon_group',
			instance_url: url.value,
			group_name: group.value
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response) {
				group.classList.add("input-valid");
				group.classList.remove("input-warning");
				groupIcon.classList.add("icon-valid");
				groupIcon.classList.remove("icon-warning");
				url.classList.add("input-valid");
				url.classList.remove("input-warning");
				urlIcon.classList.add("icon-valid");
				urlIcon.classList.remove("icon-warning");
			} else {
				group.classList.add("input-warning");
				group.classList.remove("input-valid");
				groupIcon.classList.add("icon-warning");
				groupIcon.classList.remove("icon-valid");
			}
		},"json");
	}

	function resetInstanceField() {
		url.classList.remove("input-valid");
		urlIcon.classList.remove("icon-valid");
	}

	function checkGroupFields() {
		for(var i = 0; i < groups.length; i++) {
			checkGroupField(groups[i])
		}
	}

	function removeGroup(event){
		let group = $(event.target)[0].parentNode;
		group.parentNode.removeChild(group);
	}

	function addGroup(event) {
		let group = document.getElementsByClassName("mobilizon-group-item")[0];
		var groupTemplate = group.cloneNode(true); // the true is for deep cloning
		groupTemplate.children[0].value = '';
		groupTemplate.children[0].classList.remove("input-warning");
		groupTemplate.children[0].classList.remove("input-valid");
		groupTemplate.children[1].classList.remove("icon-warning");
		groupTemplate.children[1].classList.remove("icon-valid");
		$(groupTemplate.children[0]).bind("keyup", checkGroupField);
		$(groupTemplate.children[0]).bind("change",function () {
			setTimeout(function(){ checkGroupField; }, 100);
		});
		$(groupTemplate.children[2]).bind("click", removeGroup);
		$('#mobilizon-group-input-wrapper').append(groupTemplate);
	}

	$("#mobilizon-add-group").on("click", addGroup);

	var removeButtons = $(".mobilizon-remove-group")

	for(var i = 0; i < removeButtons.length; i++) {
		$(removeButtons[i]).bind("click", removeGroup);
	}

	$(url).on("paste",function () {
		setTimeout(function(){ checkInstanceField(); }, 100);
	});
	$(url).on("keyup change", checkInstanceField);

	for(var i = 0; i < groups.length; i++) {
		$(groups[i]).bind("keyup change", checkGroupField);
		$(groups[i]).on("paste",function () {
			 	setTimeout(function(){ checkGroupField; },  100);
		});
	}

	if ( url.value != '' ) {
		checkInstanceField();
	}

});
