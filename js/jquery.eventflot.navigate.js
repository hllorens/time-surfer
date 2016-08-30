/*
Flot plugin for adding panning and zooming capabilities to a plot.

The default behaviour is double click and scrollwheel up/down to zoom
in, drag to pan. The plugin defines plot.zoom({ center }),
plot.zoomOut() and plot.pan(offset) so you easily can add custom
controls. It also fires a "plotpan" and "plotzoom" event when
something happens, useful for synchronizing plots.

Example usage:

  plot = $.plot(...);
  
  // zoom default amount in on the pixel (100, 200) 
  plot.zoom({ center: { left: 10, top: 20 } });

  // zoom out again
  plot.zoomOut({ center: { left: 10, top: 20 } });

  // pan 100 pixels to the left and 20 down
  plot.pan({ left: -100, top: 20 })


Options:

  zoom: {
    interactive: false
    trigger: "dblclick" // or "click" for single click
    amount: 1.5         // 2 = 200% (zoom in), 0.5 = 50% (zoom out)
  }
  
  pan: {
    interactive: false
  }

  xaxis, yaxis, x2axis, y2axis: {
    zoomRange: null  // or [number, number] (min range, max range)
    panRange: null   // or [number, number] (min, max)
  }
  
"interactive" enables the built-in drag/click behaviour. "amount" is
the amount to zoom the viewport relative to the current range, so 1 is
100% (i.e. no change), 1.5 is 150% (zoom in), 0.7 is 70% (zoom out).

"zoomRange" is the interval in which zooming can happen, e.g. with
zoomRange: [1, 100] the zoom will never scale the axis so that the
difference between min and max is smaller than 1 or larger than 100.
You can set either of them to null to ignore.

"panRange" confines the panning to stay within a range, e.g. with
panRange: [-10, 20] panning stops at -10 in one end and at 20 in the
other. Either can be null.
*/


// First two dependencies, jquery.event.drag.js and
// jquery.mousewheel.js, we put them inline here to save people the
// effort of downloading them.

/*
jquery.event.drag.js ~ v1.5 ~ Copyright (c) 2008, Three Dub Media (http://threedubmedia.com)  
Licensed under the MIT License ~ http://threedubmedia.googlecode.com/files/MIT-LICENSE.txt
*/
(function(E){E.fn.drag=function(L,K,J){if(K){this.bind("dragstart",L)}if(J){this.bind("dragend",J)}return !L?this.trigger("drag"):this.bind("drag",K?K:L)};var A=E.event,B=A.special,F=B.drag={not:":input",distance:0,which:1,dragging:false,setup:function(J){J=E.extend({distance:F.distance,which:F.which,not:F.not},J||{});J.distance=I(J.distance);A.add(this,"mousedown",H,J);if(this.attachEvent){this.attachEvent("ondragstart",D)}},teardown:function(){A.remove(this,"mousedown",H);if(this===F.dragging){F.dragging=F.proxy=false}G(this,true);if(this.detachEvent){this.detachEvent("ondragstart",D)}}};B.dragstart=B.dragend={setup:function(){},teardown:function(){}};function H(L){var K=this,J,M=L.data||{};if(M.elem){K=L.dragTarget=M.elem;L.dragProxy=F.proxy||K;L.cursorOffsetX=M.pageX-M.left;L.cursorOffsetY=M.pageY-M.top;L.offsetX=L.pageX-L.cursorOffsetX;L.offsetY=L.pageY-L.cursorOffsetY}else{if(F.dragging||(M.which>0&&L.which!=M.which)||E(L.target).is(M.not)){return }}switch(L.type){case"mousedown":E.extend(M,E(K).offset(),{elem:K,target:L.target,pageX:L.pageX,pageY:L.pageY});A.add(document,"mousemove mouseup",H,M);G(K,false);F.dragging=null;return false;case !F.dragging&&"mousemove":if(I(L.pageX-M.pageX)+I(L.pageY-M.pageY)<M.distance){break}L.target=M.target;J=C(L,"dragstart",K);if(J!==false){F.dragging=K;F.proxy=L.dragProxy=E(J||K)[0]}case"mousemove":if(F.dragging){J=C(L,"drag",K);if(B.drop){B.drop.allowed=(J!==false);B.drop.handler(L)}if(J!==false){break}L.type="mouseup"}case"mouseup":A.remove(document,"mousemove mouseup",H);if(F.dragging){if(B.drop){B.drop.handler(L)}C(L,"dragend",K)}G(K,true);F.dragging=F.proxy=M.elem=false;break}return true}function C(M,K,L){M.type=K;var J=E.event.handle.call(L,M);return J===false?false:J||M.result}function I(J){return Math.pow(J,2)}function D(){return(F.dragging===false)}function G(K,J){if(!K){return }K.unselectable=J?"off":"on";K.onselectstart=function(){return J};if(K.style){K.style.MozUserSelect=J?"":"none"}}})(jQuery);


/* jquery.mousewheel.min.js
 * Copyright (c) 2009 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 *
 * Version: 3.0.2
 * 
 * Requires: 1.2.2+
 */
(function(c){var a=["DOMMouseScroll","mousewheel"];c.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var d=a.length;d;){this.addEventListener(a[--d],b,false)}}else{this.onmousewheel=b}},teardown:function(){if(this.removeEventListener){for(var d=a.length;d;){this.removeEventListener(a[--d],b,false)}}else{this.onmousewheel=null}}};c.fn.extend({mousewheel:function(d){return d?this.bind("mousewheel",d):this.trigger("mousewheel")},unmousewheel:function(d){return this.unbind("mousewheel",d)}});function b(f){var d=[].slice.call(arguments,1),g=0,e=true;f=c.event.fix(f||window.event);f.type="mousewheel";if(f.wheelDelta){g=f.wheelDelta/120}if(f.detail){g=-f.detail/3}d.unshift(f,g);return c.event.handle.apply(this,d)}})(jQuery);




(function ($) {
    var options = {
        xaxis: {
            zoomRange: null, // or [number, number] (min range, max range)
            panRange: null // or [number, number] (min, max)
        },
        zoom: {
            interactive: false,
            trigger: "dblclick", // or "click" for single click
            amount: 1.5 // how much to zoom relative to current position, 2 = 200% (zoom in), 0.5 = 50% (zoom out)
        },
        pan: {
            interactive: false
        }
    };

    function init(plot) {
        function bindEvents(plot, eventHolder) {
            var o = plot.getOptions();
            if (o.zoom.interactive) {
                function clickHandler(e, zoomOut) {
                    var c = plot.offset();
                    c.left = e.pageX - c.left;
                    c.top = e.pageY - c.top;
                    if (zoomOut)
                        plot.zoomOut({ center: c });
                    else
                        plot.zoom({ center: c });
                }
                
                eventHolder[o.zoom.trigger](clickHandler);

                eventHolder.mousewheel(function (e, delta) {
                    clickHandler(e, delta < 0);
                    return false;
                });
            }
            if (o.pan.interactive) {
                var prevCursor = 'default', pageX = 0, pageY = 0;
                
                eventHolder.bind("dragstart", { distance: 10 }, function (e) {
                    if (e.which != 1)  // only accept left-click
                        return false;
                    eventHolderCursor = eventHolder.css('cursor');
                    eventHolder.css('cursor', 'move');
                    pageX = e.pageX;
                    pageY = e.pageY;
                });
                eventHolder.bind("drag", function (e) {
                    // unused at the moment, but we need it here to
                    // trigger the dragstart/dragend events
                });
                eventHolder.bind("dragend", function (e) {
                    eventHolder.css('cursor', prevCursor);
                    plot.pan({ left: pageX - e.pageX,
                               top: pageY - e.pageY });
                });
            }
        }



        plot.zoomOut = function (args) {
            if (!args)
                args = {};
            
            if (!args.amount)
                args.amount = plot.getOptions().zoom.amount

            args.amount = 1 / args.amount;
            plot.zoom(args);
        }
        
        plot.zoom = function (args) {
            if (!args)
                args = {};

	    // Hack H.Llorens: avoid selection bad positioning on zoom
	    plot.triggerRedrawOverlay();
            
            var axes = plot.getAxes(),
                options = plot.getOptions(),
                c = args.center,
                amount = args.amount ? args.amount : options.zoom.amount,
                w = plot.width(), h = plot.height();

            if (!c)
                c = { left: w / 2, top: h / 2 };
                
            var xf = c.left / w,
                x1 = c.left - xf * w / amount,
                x2 = c.left + (1 - xf) * w / amount,
                yf = c.top / h,
                y1 = c.top - yf * h / amount,
                y2 = c.top + (1 - yf) * h / amount;

            function scaleAxis(min, max, name) {
                var axis = axes[name],
                    axisOptions = options[name];
                
                if (!axis.used)
                    return;
                    
                min = axis.c2p(min);
                max = axis.c2p(max);
                if (max < min) { // make sure min < max
                    var tmp = min
                    min = max;
                    max = tmp;
                }


                var range = max - min, zr = axisOptions.zoomRange, pr = axisOptions.panRange;
		// Hack H.Llorens: adjust to min/max-ranges		
		if(pr){
		        if (zr && (zr[0] != null && range < zr[0])){ // < maxZoomIn
				//min=min; // leave as is
				//max=min+zr[0]; // zoomIn just the 'righter' part
				return; // better that way does not move (annoying movement)!!
			}
		        if (zr && (zr[1] != null && range > zr[1])){ // > maxZoomOut
				min=pr[0];
				max=pr[1];
		        }
		}else{
			// The origingal script
		        if (zr &&
		            ((zr[0] != null && range < zr[0]) ||
		             (zr[1] != null && range > zr[1])))
		            return;
		}



                //var pr = axisOptions.panRange; // moved up to check auto-adjusts
                if (pr) {
                    // check whether we hit the wall
                    if (pr[0] != null && pr[0] > min) {
                        delta = pr[0] - min; // positive
                        min += delta;
                        max += delta;
			// Hack H.Llorens: check if the ZoomRange max is more right than the pan range (auto-adjust)
			if(max > pr[1]){
				max=pr[1];			
			}
                    }
                    
                    if (pr[1] != null && pr[1] < max) {
                        delta = pr[1] - max; // negative
                        min += delta;
                        max += delta;
			// Hack H.Llorens: check if the ZoomRange min is more left than the pan range (auto-adjust)
			if(min < pr[0]){
				min=pr[0];
			}
                    }
                }
                

		// Hack H.Llorens: check if a ZoomIn (amount>1) action is taking out all the points (then don't do any action)
		if(name=='xaxis' && amount > 1){
			var anypoints=0, series=plot.getData();
			// series is an array of any series of events (we can compare 2 documents, see the temporal overlap, waw)
			// pointssize = n. coordenates or other data: (raduius,event type..., could be different than 2)

		        for (var iseries = 0; iseries < series.length; iseries += 1){
				if(anypoints==2) // break if we already have 2 points
				    break;
				anypoints=0; // then require 2 points of any of both series		
				for (var i = 0; i < series[iseries].datapoints.points.length; i += series[iseries].datapoints.pointsize) {
				    var x = series[iseries].datapoints.points[i];
				    if (x == null || x < min || x > max) // || y < min || y > max)
				        continue;
				    anypoints++;
				    if(anypoints==2)
					    break;
				}
			}


			if(anypoints<2)
				return;
		}
		// ----------------------------------------------------------------
            
                axisOptions.min = min;
                axisOptions.max = max;
            }

            scaleAxis(x1, x2, 'xaxis');
            //scaleAxis(x1, x2, 'x2axis');  // normally only 'xaxis' are used
            scaleAxis(y1, y2, 'yaxis');
            //scaleAxis(y1, y2, 'y2axis');  // normally only 'yaxis' are used
            
            plot.setupGrid();
            plot.draw();
            
            if (!args.preventEvent)
                plot.getPlaceholder().trigger("plotzoom", [ plot ]);
        }






	// Hector Llorens. New function for date focusing
	plot.datefocus = function (args) {
		var date= ""+args.date,
	                axes = plot.getAxes(), options = plot.getOptions();
		    plot.triggerRedrawOverlay();

            	if (date.lenth==0)
                	return false;


		var day="1",month="0", year="2010", dateArr = [];

		if(date.indexOf("/")!=-1){
			dateArr=date.split("/");
			if(dateArr.length == 2){
				month=dateArr[0]-1;
				year=dateArr[1];
			}else{
				if(dateArr.length == 3){
					day=dateArr[0];
					month=dateArr[1]-1;
					year=dateArr[2];
				}else{
					alert("Focus date ("+date+"): Unknown format"); return false;
				}
			}
		}else{
			dateArr=date.split("-");
			if(dateArr[0].length==4 || dateArr.length == 1){
				if(dateArr.length > 1){month=dateArr[1]-1;}
				if(dateArr.length > 2){day=dateArr[2];}
				year=dateArr[0];
			}else{
				if(dateArr.length == 2){
					month=dateArr[0]-1;
					year=dateArr[1];
				}else{
					if(dateArr.length == 3){
						day=dateArr[0];
						month=dateArr[1]-1;
						year=dateArr[2];
					}else{
						alert("Focus date ("+date+"): Unknown format"); return false;
					}
				}
			}
		}
		var d = new Date(year,month,day);

		//var d=new Date(date);
		if(d=="Invalid Date"){
			alert("Focus date ("+date+"): "+d); return false;
		}

		granularity=0.3;
		if(date.length==7)
			granularity=2;
		if(date.length==10)
			granularity=60;
			             
		var time=d.getTime();
		//alert("timeline should be posicioned to "+date+"  "+time);
		
                var axis = axes['xaxis'],
                    axisOptions = options['xaxis'];
		var series=plot.getData();
		var minx=series[0].datapoints.points[0];
		var maxx=series[0].datapoints.points[(series[0].datapoints.points.length)-(series[0].datapoints.pointsize)];		
		for (var i = 1; i < series.length; ++i) {
			if(minx>series[i].datapoints.points[0])
				minx=series[i].datapoints.points[0];
			if(maxx<series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)])
				maxx=series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)];
		}

		if(time>=(minx-86400000) && time<=(maxx+86400000)){  // 86400000 one day margin
			var delta=Math.floor(31536000000/granularity);
			if((time-delta)>minx){
			        axisOptions.min = time-delta;
			}else{
				axisOptions.min=minx;
			}
			if((time+delta)<maxx){
			        axisOptions.max = time+delta;
			}else{
				axisOptions.max=maxx;
			}
		}else{
			alert("The date ("+date+" "+time+") is out of the timeline. Please, use dates within the represented period.");			
		}
            plot.setupGrid();
            plot.draw();
            
            if (!args.preventEvent)
                plot.getPlaceholder().trigger("plotpan", [ plot ]);
	}




	// Hector Llorens. New function for period focusing
	plot.periodfocusDate = function (args) {
		var date1=""+args.date1, date2=""+args.date2;
		              	

		var day="1",month="0", year="2010", dateArr = [];

		if(date1.indexOf("/")!=-1){
			dateArr=date1.split("/");
			if(dateArr.length == 2){
				month=dateArr[0]-1;
				year=dateArr[1];
			}else{
				if(dateArr.length == 3){
					day=dateArr[0];
					month=dateArr[1]-1;
					year=dateArr[2];
				}else{
					alert("Focus date1 ("+date1+"): Unknown format"); return false;
				}
			}
		}else{
			dateArr=date1.split("-");
			if(dateArr[0].length==4 || dateArr.length == 1){
				if(dateArr.length > 1){month=dateArr[1]-1;}
				if(dateArr.length > 2){day=dateArr[2];}
				year=dateArr[0];
			}else{
				if(dateArr.length == 2){
					month=dateArr[0]-1;
					year=dateArr[1];
				}else{
					if(dateArr.length == 3){
						day=dateArr[0];
						month=dateArr[1]-1;
						year=dateArr[2];
					}else{
						alert("Focus date1 ("+date1+"): Unknown format"); return false;
					}
				}
			}
		}
		var d = new Date(year,month,day);
		//var d=new Date(date1);
		if(d=="Invalid Date"){
			alert("Period start date ("+date1+"): " + d); return false;
		}
		var time1=d.getTime();

		var day="1",month="0", year="2010", dateArr = [];

		if(date2.indexOf("/")!=-1){
			dateArr=date2.split("/");
			if(dateArr.length == 2){
				month=dateArr[0]-1;
				year=dateArr[1];
			}else{
				if(dateArr.length == 3){
					day=dateArr[0];
					month=dateArr[1]-1;
					year=dateArr[2];
				}else{
					alert("Focus date2 ("+date2+"): Unknown format"); return false;
				}
			}
		}else{
			dateArr=date2.split("-");
			if(dateArr[0].length==4 || dateArr.length == 1){
				if(dateArr.length > 1){month=dateArr[1]-1;}
				if(dateArr.length > 2){day=dateArr[2];}
				year=dateArr[0];
			}else{
				if(dateArr.length == 2){
					month=dateArr[0]-1;
					year=dateArr[1];
				}else{
					if(dateArr.length == 3){
						day=dateArr[0];
						month=dateArr[1]-1;
						year=dateArr[2];
					}else{
						alert("Focus date2 ("+date2+"): Unknown format"); return false;
					}
				}
			}
		}
		var d = new Date(year,month,day);
		//var d=new Date(date2);
		if(d=="Invalid Date"){
			alert("Period end date2 ("+date2+"): " + d); return false;
		}
		var time2=d.getTime();




		
		if(args.margin == "include"){		
			timelinemargin=(time2-time1)/10;
			time1=time1-timelinemargin;
			time2=time2+timelinemargin;		
		}

		if(args.margin == "minimum"){		
			timelinemargin=(time2-time1)/50;
			time1=time1-timelinemargin;
			time2=time2+timelinemargin;		
		}
		
		plot.periodfocus({ "time1": time1, "time2": time2 });
	}



	plot.periodfocus = function (args) {
		var t1= +args.time1, t2= +args.time2,
	                axes = plot.getAxes(), options = plot.getOptions();
		    plot.triggerRedrawOverlay();
    
            if (isNaN(t1) || isNaN(t2))
                return false;

		if(t1>t2){
			var temp=t2;
			t2=t1;
			t1=temp;
		}

		if(t1==t2){
			t1=t1-52560000000; //36792000000;
			t2=t2+52560000000; //68328000000;		
		}

                var axis = axes['xaxis'],
                    axisOptions = options['xaxis'];
		var series=plot.getData();
		var minx=series[0].datapoints.points[0];
		var maxx=series[0].datapoints.points[(series[0].datapoints.points.length)-(series[0].datapoints.pointsize)];		
		for (var i = 1; i < series.length; ++i) {
			if(minx>series[i].datapoints.points[0])
				minx=series[i].datapoints.points[0];
			if(maxx<series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)])
				maxx=series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)];
		}
		
		// take margin into account
		timelinemargin=(maxx-minx)/10;
		minx=minx-timelinemargin;
		maxx=maxx+timelinemargin;

		if(t1>=minx && t1<=maxx){
			axisOptions.min=t1;			
		}else{
			alert("The earliest date ("+(new Date(t1)).getFullYear()+" "+t1+") is out of the timeline. Please, use dates within the represented period.");
			axisOptions.min=minx;
		}

		if(t2>=minx && t2<=maxx){
			axisOptions.max=t2;			
		}else{
			alert("The latest date ("+(new Date(t2)).getFullYear()+" "+t2+") is out of the timeline. Please, use dates within the represented period.");
			axisOptions.max=maxx;
		}

            plot.setupGrid();
            plot.draw();
            
            if (!args.preventEvent)
                plot.getPlaceholder().trigger("plotpan", [ plot ]);
	}







        plot.pan = function (args) {
            var l = +args.left, t = +args.top,
                axes = plot.getAxes(), options = plot.getOptions();

	    // Hack H.Llorens: avoid selection bad positioning on pan
	    plot.triggerRedrawOverlay();


            if (isNaN(l))
                l = 0;
            if (isNaN(t))
                t = 0;

            function panAxis(delta, name) {
                var axis = axes[name],
                    axisOptions = options[name],
                    min, max;
                
                if (!axis.used)
                    return;

                min = axis.c2p(axis.p2c(axis.min) + delta),
                max = axis.c2p(axis.p2c(axis.max) + delta);

                var pr = axisOptions.panRange, mywallhit=false;
                if (pr) {
                    // check whether we hit the wall
                    if (pr[0] != null && pr[0] > min) {
                        delta = pr[0] - min;
                        min += delta;
                        max += delta;
			mywallhit=true;
                    }
                    
                    if (pr[1] != null && pr[1] < max) {
                        delta = pr[1] - max;
                        min += delta;
                        max += delta;
			mywallhit=true;
                    }
                }
                

		// Hack H.Llorens: check if a PAN action is taking out the limit points (min left or max rigth) (don't do any action)
		if(name=='xaxis' && !mywallhit){
			var series=plot.getData();
			var minx=series[0].datapoints.points[0];
			var maxx=series[0].datapoints.points[(series[0].datapoints.points.length)-(series[0].datapoints.pointsize)];
			for (var i = 1; i < series.length; ++i) {
				if(minx>series[i].datapoints.points[0])
					minx=series[i].datapoints.points[0];
				if(maxx<series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)])
					maxx=series[i].datapoints.points[(series[i].datapoints.points.length)-(series[i].datapoints.pointsize)];
			}


			if(minx!=null && minx>max){
	                        var delta2 = minx - max;
				max=minx;
				min=min+delta2; // move left

			}else{				
				if(maxx!=null && maxx<min){
			                var delta2 = min - maxx;
					min=maxx;					
					max=max-delta2; // move right
				}
			}
		}
		// ----------------------------------------------------------------

                axisOptions.min = min;
                axisOptions.max = max;
            }

            panAxis(l, 'xaxis');
            //panAxis(l, 'x2axis'); // normally only xaxis are used
            panAxis(t, 'yaxis');
            //panAxis(t, 'y2axis'); // normally only xaxis are used
            
            plot.setupGrid();
            plot.draw();
            
            if (!args.preventEvent)
                plot.getPlaceholder().trigger("plotpan", [ plot ]);
        }
        
        plot.hooks.bindEvents.push(bindEvents);
    }
    
    $.plot.plugins.push({
        init: init,
        options: options,
        name: 'navigate',
        version: '1.1'
    });
})(jQuery);
