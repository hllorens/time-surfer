/* Javascript timegraph library for jQuery, v. 0.6.
 *
 * Released under the MIT license by Hector Llorens, September 2010.
 *
 */


    function makeTGCanvas(width, height,id) {
        var c = document.createElement('canvas');
        c.width = width;
        c.height = height;
        c.id = id;
        if ($.browser.msie) // excanvas hack
            c = window.G_vmlCanvasManager.initElement(c);
        return c;
    }

 function drawTimegraph() {  
    if ($.browser.msie) // excanvas hack
        window.G_vmlCanvasManager.init_(document); // make sure everything is setup

    var canvas2 = $(makeTGCanvas(300,300,"timegraph")).appendTo("body").get(0);
   var ctx = canvas2.getContext("2d");

   
  ctx.fillStyle = "rgb(200,0,0)";  
  ctx.fillRect (10, 10, 55, 50);  
 
  ctx.fillStyle = "rgba(0, 0, 200, 0.5)";  
  ctx.fillRect (30, 30, 55, 50);  
 }  

 function drawTimegraphEvents(canvas,item) {  

	   var ctx = canvas.getContext("2d"); 
	var arrayevents= item.extradata[4];;
	var increment=Math.floor(canvas.width/(arrayevents.length+1));
	var hashevents=new Object();
         for (var i = 0; i < arrayevents.length; ++i) {
		//ctx.fillStyle = "rgba("+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+", 0.8)";  
		
		ctx.fillStyle = "rgba("+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+", 0.8)";
	  if(arrayevents[i][3]=="OCCURRENCE"){
		ctx.fillStyle = "rgba(237,194,64, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="STATE"){
		ctx.fillStyle = "rgba(245,245,245, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="I_STATE"){
		ctx.fillStyle = "rgba(237,237,250, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="I_ACTION"){
		ctx.fillStyle = "rgba(190,180,240, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="REPORTING"){
		ctx.fillStyle = "rgba(200,250,200, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="PERCEPTION"){
		ctx.fillStyle = "rgba(150,250,150, 0.8)";  	  
	  }
	  if(arrayevents[i][3]=="ASPECTUAL"){
		ctx.fillStyle = "rgba(250,200,200, 0.8)";  	  
	  }
	  var y=Math.floor(Math.random()*240)+10;
	  ctx.fillRect ((i*increment)+40,y, 100, 20);
	  //ctx.textAlign="center";
	  ctx.font="11px sans-serif";
	  ctx.strokeText(arrayevents[i][1],(i*increment)+45,y+15);
	  hashevents[arrayevents[i][0]]=[(i*increment)+40,y];
	}
	// now check links and draw arrows!!

}

