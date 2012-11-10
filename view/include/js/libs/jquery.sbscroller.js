//jQuery vertical scroller plugin v1.7
//Author: Simon Battersby, www.simonbattersby.com 
//Documentation: http://www.simonbattersby.com/blog/vertical-scrollbar-plugin-using-jquery-ui-slider/


(function( $ ){

  $.fn.sbscroller = function( options ) {  

    var settings = {//defaults
	handleImage : false,
	handleTopImage : false,
	handleBottomImage : false,
	handleGripImage : false,
	mousewheel : false,
	autohide : false
    };
	
	return this.each(function() { 
	  if (options === 'refresh') {
		 $.extend( settings, $(this).data() );//include any previously stored options for this scroll pane
		 if (!$(this).hasClass('scroll-pane')){//traps the case where refresh has been called on slider that has not been set up
			  $(this).addClass('scroll-pane').data(options);//add a class and store the options as data against the element in case they are needed later
			  $(this).children().wrapAll('<div class="scroll-content"/>');
		 }
		 setSlider($(this));
	  }  
	  else if (options === 'reset') {
		$(this).find('.slider-vertical').slider({ value: 100 });
	  } 
	  else {
		  if ( options ) { 
			$.extend( settings, options );
		  }
		  $(this).addClass('scroll-pane').data(options);//add a class and store the options as data against the element in case they are needed later
		  $(this).children().wrapAll('<div class="scroll-content"/>');
		  setSlider($(this));
	  }
	  
    });
	
	function setSlider($scrollpane){
		
		//change the main div to overflow-hidden as we can use the slider now
		$scrollpane.css('overflow','hidden');
		
		//compare the height of the scroll content to the scroll pane to see if we need a scrollbar
		var difference = $scrollpane.find('.scroll-content').height()-$scrollpane.height();//eg it's 200px longer
		$scrollpane.data('difference',difference); 
		
		if(difference<=0 && $scrollpane.find('.slider-wrap').length>0)//scrollbar exists but is no longer required
			{
				$scrollpane.find('.slider-wrap').remove();//remove the scrollbar
				$scrollpane.find('.scroll-content').css({top:0});//and reset the top position
			}
		
		if(difference>0)//if the scrollbar is needed, set it up...
			{
			   var proportion = difference / $scrollpane.find('.scroll-content').height();//eg 200px/500px
			
			   var handleHeight = Math.round((1-proportion)*$scrollpane.height());//set the proportional height - round it to make sure everything adds up correctly later on
			   handleHeight -= handleHeight%2; 
			
			   //if the slider has already been set up and this function is called again, we may need to set the position of the slider handle
			   var contentposition = $scrollpane.find('.scroll-content').position();	
			   var sliderInitial = 100*(1-Math.abs(contentposition.top)/difference);
			
			   
			   if($scrollpane.find('.slider-wrap').length==0)//if the slider-wrap doesn't exist, insert it and set the initial value
				   {
					  $scrollpane.append('<\div class="slider-wrap" ><\div class="slider-vertical"><\/div><\/div>');//append the necessary divs so they're only there if needed
					  sliderInitial = 100;
				   }
			   
			   	$scrollpane.find('.slider-wrap').height(Math.round($scrollpane.height()));//set the height of the slider bar to that of the scroll pane

			   
			   //set up the slider 
			   $scrollpane.find('.slider-vertical').slider({
				  orientation: 'vertical',
				  min: 0,
				  max: 100,
				  value: sliderInitial,
				  slide: function(event, ui) {
					 var topValue = -((100-ui.value)*difference/100);
					 $scrollpane.find('.scroll-content').css({top:topValue});//move the top up (negative value) by the percentage the slider has been moved times the difference in height
				  },
				  change: function(event, ui) {
				  var topValue = -((100-ui.value)*($scrollpane.find('.scroll-content').height()-$scrollpane.height())/100);//recalculate the difference on change
					 $scrollpane.find('.scroll-content').css({top:topValue});//move the top up (negative value) by the percentage the slider has been moved times the difference in height
				  }	  
			   });
			
			   //set the handle height and bottom margin so the middle of the handle is in line with the slider
			   $scrollpane.find(".ui-slider-handle").css({height:handleHeight,'margin-bottom':-0.5*handleHeight});
			   var origSliderHeight = $scrollpane.height();//read the original slider height
			   var sliderHeight = origSliderHeight - handleHeight ;//the height through which the handle can move needs to be the original height minus the handle height
			   var sliderMargin =  (origSliderHeight - sliderHeight)*0.5;//so the slider needs to have both top and bottom margins equal to half the difference
			   $scrollpane.find(".ui-slider").css({height:sliderHeight,'margin-top':sliderMargin});//set the slider height and margins
			   $scrollpane.find(".ui-slider-range").css({top:-sliderMargin});//position the slider-range div at the top of the slider container
			   
			   //create elements to hold the images for the scrollbar handle if needed
			   if(settings.handleTopImage) $scrollpane.find(".ui-slider-handle").css({backgroundImage:'url('+settings.handleImage+')',backgroundRepeat:'repeat-y'});
			   if(settings.handleTopImage) $scrollpane.find(".ui-slider-handle").append('<img class="scrollbar-top" src="'+settings.handleTopImage+'"/>');
			   if(settings.handleBottomImage) $scrollpane.find(".ui-slider-handle").append('<img class="scrollbar-bottom" src="'+settings.handleBottomImage+'"/>');
			   if(settings.handleGripImage) {
				   $scrollpane.find(".ui-slider-handle").append('<img class="scrollbar-grip" src="'+settings.handleGripImage+'"/>');	
				   $scrollpane.find('.scrollbar-grip').load(function(){//wait till the image loads for Webkit
					   $scrollpane.find(".scrollbar-grip").css({marginTop:-1*Math.round(0.5*$scrollpane.find(".scrollbar-grip").height()+0.5)+'px'});
				   });
			   }
			}//end if
			 
		 //code for clicks on the scrollbar outside the slider
		$(".ui-slider").click(function(event){//stop any clicks on the slider propagating through to the code below
			event.stopPropagation();
		});
	   
		$(".slider-wrap").click(function(event){//clicks on the wrap outside the slider range
			var offsetTop = $(this).offset().top;//read the offset of the scroll pane
			var clickValue = (event.pageY-offsetTop)*100/$(this).height();//find the click point, subtract the offset, and calculate percentage of the slider clicked
			$(this).find(".slider-vertical").slider("value", 100-clickValue);//set the new value of the slider
		}); 
	
		 
		//additional code for mousewheel
		if($.fn.mousewheel){			
			$scrollpane.unmousewheel();//remove any previously attached mousewheel events
			$scrollpane.mousewheel(function(event, delta){
				var speed = Math.round(5000/$scrollpane.data('difference'));
				if (speed <1) speed = 1;
				if (speed >100) speed = 100;
				var sliderVal = $(this).find(".slider-vertical").slider("value");//read current value of the slider
				
				sliderVal += (delta*speed);//increment the current value
		 
				$(this).find(".slider-vertical").slider("value", sliderVal);//and set the new value of the slider
				
				event.preventDefault();//stop any default behaviour
			});
		}
		
		//autohide
		if(settings.autohide){
		   if (!$scrollpane.find(".slider-wrap").hasClass('slider-wrap-active')) $scrollpane.find(".slider-wrap").hide();//only hide if it's not already active - this could be the case if content is added or removed from within the scroll pane
		   $scrollpane.hover(function(){
				$scrollpane.find(".slider-wrap").show().addClass('slider-wrap-active');
				},
				function(){
				$scrollpane.find(".slider-wrap").hide().removeClass('slider-wrap-active');
				})
		}
		
		
	}

  };
})( jQuery );
