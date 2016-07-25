

var widgetApiUrl;
var apiKey;

var SelectTheme = (function(){
	var theme 		= $('.theme-wrapper');
	var theme_radio = $('input[name="widget-theme"]');

	var setSelectedTheme = function(){
		var obj = $(this);
		var selected = obj.data('theme');
		var radio = $('input[value="'+selected+'"]');

		$('input[name="widget-theme"]').val(selected);
		theme.removeClass('selected');
		theme_radio.removeAttr('checked');

		obj.addClass('selected');
		radio.attr('checked','checked');
	};

	theme.click(setSelectedTheme);
})();

var StarRating = (function(){
	var review_rating = $('.review-rating');
	
	var setStars = function(){
		var obj 			= $(this);
		var rating_value 	= obj.data('rating');
		var star_icon 		= "<i class='icon ion-android-star'>";
		
		for(x=0;x<rating_value;x++){
			obj.append(star_icon);
		}
	};

	review_rating.each(setStars);

})();

var SectionNav = (function() {
	var sections		= $('.main-content section');
	var len 			= (sections.length) - 1;
	var active_section 	= $('section.active');
	var navigator 		= $('.section-nav');
	var nav_left 		= navigator.children('.nav-left');
	var nav_right 		= navigator.children('.nav-right');
	var nav_right_btn 	= nav_right.children('button');
	var nav_left_btn 	= nav_left.children('button');
	var nav_right_value = nav_right.find('span');
	var nav_left_value  = nav_left.find('span');
	var widgetData = {name: '', container_id: '', data_selected: []};

	function updateNavButtons() {
		if(active_section.attr("id") == "widget-editor") {
			nav_left.hide();
			nav_right_value.text("Manage reviews");
		}

		else if(active_section.attr("id") == "manage-reviews") {
			nav_left.show();
			nav_right.show();
			nav_right_value.text("Generate script");
		}

		else if(active_section.attr("id") == "generate-script") {
			nav_left_value.text("Manage reviews");
		}
	}


	var displayNext = function(e) {

		e.preventDefault();

		if(active_section.index() <= len) {

			if (active_section.attr("id") == "manage-reviews") {

				var widgetTheme =  $('input[name="widget-theme"]').val();
				var widgetName = $('input[name="widget-name"]').val();
				var widgetId = $('input[name="widget-id"]').val();
				var widgetContainerId = $('input[name="widget-container-id"]').val();
				var widgetStatus = $('select[name="widget-status"] option:selected').val();
				var hasError = 0;
				var errorMessage = '';

				var selectedReview = [];
				$.each($("input[name='review-select']:checked"), function(e) { 
					var elem = $(this);
					selectedReview.push({prod_id: elem.data('productid'), id: elem.data('id')});
				});

				
				widgetData = {
					id: widgetId,
					name: widgetName,
					container_id: widgetContainerId,
					status: widgetStatus,
					theme: widgetTheme,
					data_selected: selectedReview
				};


				if (!widgetData.data_selected.length) {
					errorMessage = 'You must select at least one Product Reviews in the list.'; 
					hasError = 1;
				}
				
				if (widgetData.container_id == '') {
					errorMessage = 'You must specify the container id to where you expect the widget is showing within the page.'; 
					hasError = 1;
				}
				
				if (widgetData.name == '') {
					errorMessage = 'Widget name is required as label.'; 
					hasError = 1;
				} 

				if (hasError) {
					alert(errorMessage);
					return;
				} 

				nav_right_value.prop('disabled' , true);
				nav_right.attr('value', 'Saving Widget..');
				nav_right.attr('disabled', 'disabled');
				nav_left.hide();

				//Save Widget info in api
				$.ajax({
					url: widgetApiUrl + '?api_key=' + apiKey,
					type: 'post',
					data: widgetData,
					complete: function(res, status) {
						nav_right.hide();
						sections.removeClass('active');
						active_section.next().addClass('active');
						active_section 	= $('section.active');
						
						$('#widget-name-value').html(widgetData.name);
						$('#widget-container-value').html(widgetData.container_id);
						$('#widget-script-value').val(res.responseText);
					}
				});

				return;
			}

			sections.removeClass('active');
			active_section.next().addClass('active');
			active_section 	= $('section.active');
		}

		updateNavButtons();
	};

	var displayPrevious = function(){

		if(active_section.index() > 0){
			sections.removeClass('active');
			active_section.prev().addClass('active');
			active_section 	= $('section.active');
		}

		updateNavButtons();
	};

	nav_right_btn.click(displayNext);
	nav_left_btn.click(displayPrevious);

	
})();