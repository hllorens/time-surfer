<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Time-Surfer</title>
    <!--<link href="layout.css" rel="stylesheet" type="text/css"></link>-->
    <!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
    <!--<script language="javascript" type="text/javascript" src="excanvas_r3/excanvas.js"></script>-->
    <script language="javascript" type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <!--<script language="javascript" type="text/javascript" src="../jquery.flot.js"></script>-->
    <script language="javascript" type="text/javascript" src="js/jquery.eventflot.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.eventflot.navigate.js"></script>
    <!--<script language="javascript" type="text/javascript" src="../jquery.flot.navigate.js"></script>-->
    <script language="javascript" type="text/javascript" src="js/jquery.timegraph.js"></script>
    <style>
    .content{
	padding-left:30px;
	}
    #placeholder .button {
        position: absolute;
        cursor: pointer;
    }
    #placeholder div.button {
        font-size: smaller;
        color: #999;
        background-color: #eee;
        padding: 2px;
    }
    .message {
        padding-left: 50px;
        font-size: smaller;
    }
.close_btn {
	position absolute;
	background:url("images/close.png") no-repeat transparent;
	border:0 none;
	display:block;
	height:14px;
	overflow:hidden;
	width:14px;
	float:right;
}
.inBlue{
	color:Blue;
	font-style: italic;
}

.navphp{
	color: black;
	text-decoration: none;
}
.navphp:hover{
	text-decoration: underline;
	color: blue;
}

    </style>
 </head>
 <body>


<?php 
	if(isset($_GET['data']) && $_GET['data']!=""){
		$filename="data/".$_GET['data'].".txt.TAn";
		    if (!file_exists($filename)){
			echo "There is no data for ".$_GET['data'].".";
		    }else{
    

?>




    <h1>Time-Surfer: <?=$_GET['data']?> <?php if(isset($_GET['data2']) && $_GET['data2']!=""){echo " and ".$_GET['data2'];} ?></h1>
	<?php
    	echo "<p>Time representation of <a href=\"http://en.wikipedia.org/wiki/".substr($_GET['data'],strpos($_GET['data'],"/")+1)."\">".$_GET['data']."</a> article in Wikipedia.</p>";
	 if(isset($_GET['data2']) && $_GET['data2']!=""){
		$filename2="data/".$_GET['data2'].".txt.TAn";
		if (file_exists($filename2)){
		echo "<p>Time representation of <a href=\"http://en.wikipedia.org/wiki/".substr($_GET['data2'],strpos($_GET['data2'],"/")+1)."\">".$_GET['data2']."</a> article in Wikipedia.</p>";
		}else{
		echo "<p>The article ".$_GET['data2']." has been not annotated with TimeML yet.</p>";
		}
	} ?>

<div class="content">
	<form id="timefocus" name="timefocus" action=""><label>Focus date:</label> <input id="focusdate" name="focusdate" type="text" maxlength="16" size="16" /><input id="gofocusdate" type="button" value="Go" /> &nbsp;&nbsp;&nbsp; <label>Focus period:</label> <input id="focusperiod1" name="focusperiod1" type="text" maxlength="16" size="16" /> <input id="focusperiod2" name="focusperiod2" type="text" maxlength="16" size="16" /><input id="gofocusperiod" type="button" value="Go" /></form>
</div>



    <div id="placeholder" style="width:900px;height:300px;"></div>
    

<div class="content">
	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>Earliest year</b>: <span id="mindate" class="inBlue"></span>	-	<b>Latest year</b>: <span id="maxdate" class="inBlue"></span>	- <b>Total event groups</b>: <span id="numeventgroups" class="inBlue"></span></p>


	<form id="eventsearch" name="eventsearch" action=""><label>Search event or participants:</label> <input id="searchquery" name="searchquery" type="text" maxlength="35" size="35" /><input id="currentsearch" name="currentsearch" type="hidden" value=""> <input id="searchfilter" type="button" value="Search" /><input id="searchfilterfocus" type="button" value="Search&Focus" /><input id="searchclear" type="button" value="Clear" /></form><br />
	<form id="relationsearch" name="relationsearch" action=""><label>Search entity relations</label> - E1: <input id="searchqueryrel1" name="searchqueryrel1" type="text" maxlength="35" size="35" /> E2: <input id="searchqueryrel2" name="searchqueryrel2" type="text" maxlength="35" size="35" /><input id="currentsearchrel" name="currentsearchrel" type="hidden" value=""> <input id="relationfilter" type="button" value="Search Rels." /><input id="relationfilterfocus" type="button" value="Search&Focus" /><input id="searchclearrels" type="button" value="Clear" /></form>
	<br />
    <p class="message"></p>

    <!--<p id="hoverdata">Mouse hovers at (<span id="x">0</span>, <span id="y">0</span>). <span id="clickdata"></span></p>-->
</div>

<script id="source" language="javascript" type="text/javascript">
$(function () {
        var data = [];

        var dataurl = <?="'ajax.utf8.wrapper.php?data=".$filename."'"?>;
	var timelinemargin=0;
	var placeholder = $("#placeholder");
	var options={};
	var plot = $.plot(placeholder, data, options);

           function onDataReceived(series) {
  		        data.push(series);
			var minx=data[0]["data"][0][0];
			var maxx=data[0]["data"][data[0]["data"].length-1][0];
			for (var i = 1; i < data.length; ++i) {
				if(minx>data[i]["data"][0][0])
					minx=data[i]["data"][0][0];
				if(maxx<data[i]["data"][data[i]["data"].length-1][0])
					maxx=data[i]["data"][data[i]["data"].length-1][0];
			}
		    timelinemargin=(maxx-minx)/10;
		    options = {
			series: { points: { show: true, radius:9} },
			//1year=31536000000 & zoom range automatically adjusts by eventflot.navigate functions
			// min zoom is up to 250000 years (fair enough)
  			xaxis: { mode: "time", panRange: [minx-timelinemargin,maxx+timelinemargin], zoomRange: [10000,10000000000000000] },
			yaxis: { min:0, max:2, panRange: [0.0, 2.0], zoomRange: [0,0]},
			grid: { hoverable: true, clickable: true },
			zoom: {
			    interactive: true, // if true: mouse-wheel and double click (otherwise buttons)
			    amount: 1.25         // default 1.5 (1.25 smoother):  0.5 ()    2 (200%)   2 = 200% (zoom in), 0.5 = 50% (zoom out)
			},
			pan: {
			    interactive: true // if true: click+drag functionality (otherwise buttons)
			}
		    };

		    plot = $.plot(placeholder, data, options);
		    $("#mindate").html(plot.getMinDate());
		    $("#maxdate").html(plot.getMaxDate());
		    $("#numeventgroups").html(plot.getNumEventGroups());
		    plot.zoomOut();

    // panning arrows
    function addArrow(dir, right, top, offset) {
        $('<img class="button" src="images/arrow-' + dir + '.gif" style="right:' + right + 'px;top:' + top + 'px">').appendTo(placeholder).click(function (e) {
            e.preventDefault();
            plot.pan(offset);
        });
    }


		    addArrow('left', 55, 40, { left: -100 });
		    addArrow('right', 25, 40, { left: 100 });

    // add zoom out button 
    $('<div class="button" style="right:20px;top:20px">zoom out</div>').appendTo(placeholder).click(function (e) {
        e.preventDefault();
        plot.zoomOut();
    });

    // show pan/zoom messages to illustrate events 
    placeholder.bind('plotpan', function (event, plot) {
        var axes = plot.getAxes();
        // Show panning info
        /*$(".message").html("Panning to x: "  + axes.xaxis.min.toFixed(2)
                           + " &ndash; " + axes.xaxis.max.toFixed(2)
                           + " and y: " + axes.yaxis.min.toFixed(2)
                           + " &ndash; " + axes.yaxis.max.toFixed(2));*/
    });

    placeholder.bind('plotzoom', function (event, plot) {
        var axes = plot.getAxes();
        // Show zooming info
        /*$(".message").html("Zooming to x: "  + axes.xaxis.min.toFixed(2)
                           + " &ndash; " + axes.xaxis.max.toFixed(2)
			   + " (total "+ (axes.xaxis.max.toFixed(2)-axes.xaxis.min.toFixed(2)) +") "
                           + " and y: " + axes.yaxis.min.toFixed(2)
                           + " &ndash; " + axes.yaxis.max.toFixed(2));*/
    });

		
    $("#placeholder").bind("plothover", function (event, pos, item) {
        // Show hovering information
        //$("#x").text(pos.x.toFixed(2));
        //$("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.datapoint) {
                    previousPoint = item.datapoint;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2),
			itemnumevents=item.extradata[2],itemdate=item.extradata[3],arrayevents=item.extradata[4];
			var itemtext ="", detail_lines=[];

			for (var i = 0; i < arrayevents.length; ++i){
				var detailfound=0;
				// If there are two "equal" sentences/events they only one is shown and the rest omitted
				for (var j = 0; j < detail_lines.length; ++j){
					if(detail_lines[j]==arrayevents[i][2]){
						detailfound=1;
						break;
					}
				}
				if(detailfound==0){
					detail_lines.push(arrayevents[i][2]);
	  				itemtext+="-> "+arrayevents[i][2]+"<br />";
				}
			}
                    
                    showTooltip(item.pageX, item.pageY,
                                " Date: <b>" + itemdate +"</b> (<b>"+itemnumevents+"</b> events)<br />Events detail:<br />"+itemtext);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
    });


    $("#placeholder").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text("You clicked point " + item.dataIndex);
            //$("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
	    /*if(plot.indexOfHighlight(series,item.datapoint)>=0){
	            plot.unhighlight(item.series, item.datapoint);
	    }else{
	            plot.highlight(item.series, item.datapoint);
	    }*/
	    //plot.showGraph(item.series, item.datapoint)

   	    $("#tooltip").remove();
	    //showTimegraph(item.pageX, item.pageY,item);
	    showEventGroup(item.pageX, item.pageY,item);

        }else{
            $("#clickdata").text("Clicked outside points");
	}
    });


    



    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }


    var previousPoint = null;

    function showTimegraph(x, y, item) {
	var closeb=$('<div id="closegraph" class="close_btn"></div>').click(function (e) {
		e.preventDefault();
		$("#timegraph"+x).remove();});


	    var canvas2 = $(makeTGCanvas(780,300,"timegraph")).get(0);
           drawTimegraphEvents(canvas2,item);

        $('<div id="timegraph' + x + '">&nbsp;&nbsp;<b>'+item.extradata[3]+'</b><br /></div>').css( {
            position: 'absolute',
            display: 'none',
            top: 120,
            left: 20,
            border: '2px solid #55f',
            padding: '2px',
            'background-color': '#bcf',
            opacity: 0.95
        }).appendTo("body").prepend(closeb).append(canvas2).fadeIn(400);
	
    }




    function showEventGroup(x, y, item) {
	var closeb=$('<div id="closegraph" class="close_btn"></div>').click(function (e) {e.preventDefault();$("#grouptext"+x).remove();});	
            $("#tooltip").remove();
            var itemnumevents=item.extradata[2],itemdate=item.extradata[3],arrayevents=item.extradata[4], itemtext ="", detail_lines=[];

		for (var i = 0; i < arrayevents.length; ++i){
			var detailfound=0;
			for (var j = 0; j < detail_lines.length; ++j){
				if(detail_lines[j]==arrayevents[i][2]){
					detailfound=1;
					break;
				}
			}
			if(detailfound==0){
				detail_lines.push(arrayevents[i][2]);
  				itemtext+='-> <a class="navphp" target="blank" href="data/navphp/'+arrayevents[i][7]+'.nav.php#'+arrayevents[i][8]+'">'+arrayevents[i][2]+"</a><br />";
			}
		}
            
        $('<div id="grouptext' + x + '">&nbsp;&nbsp;<b>'+itemdate+'</b> (<b>'+itemnumevents+'</b> events)<br /><br /><br /></div>').css( {
            position: 'absolute',
            display: 'none',
            top: 120,
            left: 20,
            width: '880px',
            border: '2px solid #55f',
            padding: '2px',
            'background-color': '#bcf',
            opacity: 0.95
        }).appendTo("body").prepend(closeb).append(itemtext+"<br /><br />").fadeIn(400);
	
    }



}



        $.ajax({
		url: dataurl,
		contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		success: function(data) {
		onDataReceived(data);
  	}
        });



<?php
	if(isset($_GET['data2']) && $_GET['data2']!=""){
		$filename2="data/".$_GET['data2'].".txt.TAn";
		    if (file_exists($filename2)){
?>
	var dataurl2 = <?="'ajax.utf8.wrapper.php?data=".$filename2."'"?>;
        $.ajax({
            url: dataurl2,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
		success: function(data) {
		onDataReceived(data);
 	 }
        });
<?php
}}
?>

	$(function(){$('#focusdate').keydown(function(e){
            if (e.keyCode == 13) {
                $('#gofocusdate').click();
                return false;
            }
	});});
	$(function(){$('#focusperiod1').keydown(function(e){
            if (e.keyCode == 13) {
                $('#gofocusperiod').click();
                return false;
            }
	});});
	$(function(){$('#focusperiod2').keydown(function(e){
            if (e.keyCode == 13) {
                $('#gofocusperiod').click();
                return false;
            }
	});});
	$(function(){$('#searchquery').keydown(function(e){
            if (e.keyCode == 13) {
                $('#searchfilter').click();
                return false;
            }
	});});
	$(function(){$('#searchqueryrel2').keydown(function(e){
            if (e.keyCode == 13) {
                $('#relationfilter').click();
                return false;
            }
	});});







	$("#gofocusdate").click(function() {
		var date= jQuery.trim($("#focusdate").val());		
		plot.datefocus({ "date": date });
	});


	$("#gofocusperiod").click(function() {
		plot.periodfocusDate({ "date1": jQuery.trim($("#focusperiod1").val()), "date2": jQuery.trim($("#focusperiod2").val()), "margin": "minimum" });
	});



	$("#searchfilter").click(function() {
		var query= jQuery.trim($("#searchquery").val());
		//alert("query "+query);
		$("#currentsearch").val("");
		plot.searchclear();
		if(query != ""){
			if(plot.searchfilter(query)){
				$("#currentsearch").val(query);
				//plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull(), "margin": "include" });
				$("#mindate").html(plot.getMinDate());
				$("#maxdate").html(plot.getMaxDate());
				$("#numeventgroups").html(plot.getNumEventGroups());
			}else{
				$("#currentsearch").val("");
				plot.searchclear();
				//plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
				//plot.zoomOut();
			}
			$("#mindate").html(plot.getMinDate());
			$("#maxdate").html(plot.getMaxDate());
			$("#numeventgroups").html(plot.getNumEventGroups());
		}
	});
	

	$("#searchfilterfocus").click(function() {
		var query= jQuery.trim($("#searchquery").val());
		//alert("query "+query);
		$("#currentsearch").val("");
		plot.searchclear();
		if(query != ""){
			if(plot.searchfilter(query)){
				$("#currentsearch").val(query);
				plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull(), "margin": "include" });
				$("#mindate").html(plot.getMinDate());
				$("#maxdate").html(plot.getMaxDate());
				$("#numeventgroups").html(plot.getNumEventGroups());
			}else{
				$("#currentsearch").val("");
				plot.searchclear();
				plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
				plot.zoomOut();
			}
			$("#mindate").html(plot.getMinDate());
			$("#maxdate").html(plot.getMaxDate());
			$("#numeventgroups").html(plot.getNumEventGroups());
		}
	});

	
	

	$("#relationfilter").click(function() {
		var e1= jQuery.trim($("#searchqueryrel1").val());
		var e2= jQuery.trim($("#searchqueryrel2").val());
		plot.searchclear();
		//plot.searchclearrels();
		if(e1!="" && e2!=""){
			if(plot.searchrels(e1,e2)){
				//plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull(), "margin": "include" });
				$("#mindate").html(plot.getMinDate());
				$("#maxdate").html(plot.getMaxDate());
				$("#numeventgroups").html(plot.getNumEventGroups());
			}else{
				plot.searchclear();
				//plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
				//plot.zoomOut();
			}
		}
		//alert("Under construction");
	});
		
	$("#relationfilterfocus").click(function() {
		var e1= jQuery.trim($("#searchqueryrel1").val());
		var e2= jQuery.trim($("#searchqueryrel2").val());
		plot.searchclear();
		//plot.searchclearrels();
		if(e1!="" && e2!=""){
			if(plot.searchrels(e1,e2)){
				plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull(), "margin": "include" });
				$("#mindate").html(plot.getMinDate());
				$("#maxdate").html(plot.getMaxDate());
				$("#numeventgroups").html(plot.getNumEventGroups());
			}else{
				plot.searchclear();
				plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
				plot.zoomOut();
			}
		}
		//alert("Under construction");
	});




	$("#searchclear").click(function() {
		$("#currentsearch").val("");
		plot.searchclear();
		plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
		plot.zoomOut();
		$("#mindate").html(plot.getMinDate());
		$("#maxdate").html(plot.getMaxDate());
		$("#numeventgroups").html(plot.getNumEventGroups());
	});

	$("#searchclearrels").click(function() {
		$("#currentsearch").val("");
		plot.searchclear();
		//plot.searchclearrels();
		plot.periodfocusDate({ "date1": plot.getMinDateFull(), "date2": plot.getMaxDateFull() });
		plot.zoomOut();
		$("#mindate").html(plot.getMinDate());
		$("#maxdate").html(plot.getMaxDate());
		$("#numeventgroups").html(plot.getNumEventGroups());

		//alert("Under construction");
	});



});
</script>
<a href="index.php">Return to main menu</a>
<?php
}
}else{
?>
    <h1>Time-Surfer: Time-based access to the information</h1>
    
<h2>Select Example</h2>
<ul>

<li>History facts</li>
<ol>
<?php
$myDirectory = opendir("data/history");
while($entryName = readdir($myDirectory)) {
 $entryLength=strlen($entryName);
 if (substr($entryName, 0, 1) != "." && substr($entryName,$entryLength-3,$entryLength) == "TAn"){ // don't list hidden files
  echo "<li><a href=\"index.php?data=history/".substr($entryName, 0, strpos($entryName,".txt.TAn"))."\">".substr($entryName, 0, strpos($entryName,".txt.TAn"))."</a> time representation (Wikipedia)</li>";
 }
}
closedir($myDirectory);	
?>
</ol>
<br />
<li>Biographical Documents</li>
<ol>
<?php
$myDirectory = opendir("data/biographical");
while($entryName = readdir($myDirectory)) {
 $entryLength=strlen($entryName);
 if (substr($entryName, 0, 1) != "." && substr($entryName,$entryLength-3,$entryLength) == "TAn"){ // don't list hidden files
  echo "<li><a href=\"index.php?data=biographical/".substr($entryName, 0, strpos($entryName,".txt.TAn"))."\">".substr($entryName, 0, strpos($entryName,".txt.TAn"))."</a> time representation (Wikipedia)</li>";
 }
}
closedir($myDirectory);	
?>
</ol>
<br /><br />
<li>Spanish - Autores de la Literatura Española</li>
<ol>
<?php
$myDirectory = opendir("data/literatura");
while($entryName = readdir($myDirectory)) {
 $entryLength=strlen($entryName);
 if (substr($entryName, 0, 1) != "." && substr($entryName,$entryLength-3,$entryLength) == "TAn"){ // don't list hidden files
  echo "<li><a href=\"index.php?data=literatura/".substr($entryName, 0, strpos($entryName,".txt.TAn"))."\">".substr($entryName, 0, strpos($entryName,".txt.TAn"))."</a> time representation (Wikipedia)</li>";
 }
}
closedir($myDirectory);	
?>
</ol>

<br />
<li>Spanish - Comparativa de autores de la literatura Española</li>
<ol>
	<li><a href="index.php?data=literatura/Federico_García_Lorca&data2=literatura/Benito_Pérez_Galdós">Lorca y Galdós</a></li>
	<li><a href="index.php?data=literatura/Gabriel_García_Márquez&data2=literatura/Mario_Vargas_Llosa">Márquez y Llosa</a></li>
	<li><a href="index.php?data=literatura/Miguel_de_Cervantes&data2=literatura/Francisco_de_Quevedo">Cervantes y Quevedo</a></li>
</ol>
<br /><br />
<li>Spanish - Novelas</li>
<ol>
<?php
$myDirectory = opendir("data/novelas");
while($entryName = readdir($myDirectory)) {
 $entryLength=strlen($entryName);
 if (substr($entryName, 0, 1) != "." && substr($entryName,$entryLength-3,$entryLength) == "TAn"){ // don't list hidden files
  echo "<li><a href=\"index.php?data=novelas/".substr($entryName, 0, strpos($entryName,".txt.TAn"))."\">".substr($entryName, 0, strpos($entryName,".txt.TAn"))."</a> time representation Biblioteca virtual</li>";
 }
}
closedir($myDirectory);	
?>
</ol>
<br /><br />




</ul>




<!--<p>Our approach may complement Google's News Timeline offering the possibility of exploring within a document <a href="http://www.google.com/search?q=timeline&hl=en&sa=X&tbs=tl:1,tll:2003/01,tlh:2003/03&prmd=i&ei=9uFNTOTkBZm8jAf616XYDA&ved=0CHMQyQEoBQ">Google Timeline</a>.</p>-->


<?php


}


?>


<br />
<br />
<br />
<div>Contact: <a href="http://www.cognitionis.com/hector-llorens/">Hector Llorens</a>, University of Alicante, Spain</div>

 </body>
</html>
