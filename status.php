<!DOCTYPE html>
<?php session_start(); 
 if( $_REQUEST["idd"] != session_id() || $_SESSION["user"]=="") {  header('Location:/Login.php');}
 
?>
<html>

	<?php $men=2; include "header.html"; ?>
	
							<li><a href="#" class="SSa rightli"><h4 id="SS"><span>Services Status</span></h4></a></li>
							<li><a href="#" class="Logsa rightli"><h4 id="Logs"><span>Logs</span></h4></a></li>
							
						</ul>
						<?php include "SS.php" ?>
						<?php include "Logs.php" ?>
					</div>
				</div>
			
			</div>
			<div class="row">
			<div id="chart1"></div>	
			<footer class="footer"> Errors
			</footer>
		</div>
	</div>
	</div>
	<script src='js/jquery.js'></script>
	<script src="js/bootstrap.min.js" ></script>
	<script src='js/bootstrap-datepicker.js'></script>
		<script src="js/bootstrap-timepicker.js"></script>
		<script src='js/jquery.jqplot.min.js'></script>
		<script src='js/excanvas.min.js'></script>
		<script src="js/jqplot.dateAxisRenderer.min.js"></script>
		
		<script>
			var msgdata= "no no no";
			var msgs="no data";
			var datalogf = [];
			var betweend = [];
			var oldSdatec="1"; var oldEdatec="2";
			var newSdatec="3"; var newEdatec="4";
			var datemod="";
			var plotflag = 0;
			var config = 1;
			var disktime="23:3434:34534";
			var disktimenew="34543:43543:34";
			var logtime=[]; var logtimenew="34543:43543:34";
			var dl =[[[0,0]],[[0,0]]];
			var plotbw; var plotrs; var plotws; var plotsvct; var plotqlen; var plotdl;
			var traffictime = "55:55:55";
			var trafficnewtime = "new 3444"
			var logstatus=[];
			var logcache=3;
			var obj=[];
			var disksval="hi"
			var dater;
			var page=0;
			var reqpage=0;
			var activepage=0; var lastpage=-1;
			
			var sineRenderer = function() {
				//var data = [[]];
				for (var i=0; i<13; i+=0.5) {
					dl[0].push([i, Math.sin(i)]);
				}
				
				return dl;
			};
			function searchmsg (strarr,str) {
				for (var j=0; j< strarr.length; j++) {
					if (strarr[j].match(str)) return j;
				};
				return -1;
			};
			
			$.ajax({
				url : "Data/msgs.txt",
				dataType: "text",
				success : function (data) {
					
					 msgdata=data;
					msgs=msgdata.split("\n");
						}
				});
			
			function refreshList(req,listid,fileloc) {
				$.get("requestdatein.php", { file: fileloc+"updated" }, function(data){
					var cdata=jQuery.parseJSON(data);
					disktimenew=cdata.updated;
				});
				if(disktimenew!=disktime)
				{ 
					disktime=disktimenew;
					$(listid+' option').remove();
					$.get("requestdata.php", { file: fileloc }, function(data){
						var jdata = jQuery.parseJSON(data);
						//console.log(data);
						
						$.each(jdata, function(i,v) {
						//	console.log(i,k);
							 $(listid).append($('<option>').text(i).val(v)); 
							
						});
					});
				}
			};

//			var plot1 = $.jqplot('chart1',dl);
			$("#Stimec").timepicker({
					appendWidgetTo: 'body',
					minuteStep: 1,
					showMeridian: false,
			});

			$("#Stime").timepicker({
								appendWidgetTo: 'body',
                minuteStep: 1,
								showMeridian: false,
			});
			$('.input-daterange').datepicker({
				format: "mm/dd/yyyy",
				weekStart: 6,
				startDate: "1/1/2014",
				todayBtn: "linked",
				keyboardNavigation: false,
				autoclose: true,
				todayHighlight: true
			});
			$(".SS").hide(); $(".Logs").hide(); 
			$("#SS").click(function (){ 
				if(config == 1 ) { 
					var userpriv="false";
					var curuser="<?php echo $_SESSION["user"] ?>";
					$.get("requestdata.php", { file: 'Data/userpriv.txt' },function(data){ 
						var gdata = jQuery.parseJSON(data);
						for (var prot in gdata){
							if(gdata[prot].user=="<?php echo $_SESSION["user"] ?>") {
								userpriv=gdata[prot].Service_Charts
							}
						};
						if( userpriv=="true" | curuser=="admin" ) {
							config= 0; $("h2").css("background-image","url('img/SS.png')").text("Service Status"); $(".SS").show();updatechartarea(); 
						} 
					});
				};
			});
			$("#pnext").click(function(){  
				activepage=activepage+1;
				logstatus[activepage]=20;
			});
			$("#pprev").click(function(){  
				if( activepage > 0 )  { console.log("hi"); activepage=activepage-1; logstatus[activepage]=20; }
			});
			$("#refresh").click(function(){  
				logstatus[0]=10;
			});
			$("#INFO").click(function() {
				$(".datarow").hide();
				if($("#INFO").is(":checked")) { $(".info").show(); }
				if($("#Warning").is(":checked")) { $(".warning").show(); }
				if($("#Error").is(":checked")) { $(".error").show(); }
			});
			$("#Warning").click(function() {
				$(".datarow").hide();
				if($("#INFO").is(":checked")) { $(".info").show(); }
				if($("#Warning").is(":checked")) { $(".warning").show(); }
				if($("#Error").is(":checked")) { $(".error").show(); }
			});
			$("#Error").click(function() {
				$(".datarow").hide();
				if($("#INFO").is(":checked")) { $(".info").show(); }
				if($("#Warning").is(":checked")) { $(".warning").show(); }
				if($("#Error").is(":checked")) { $(".error").show(); }
			});
			$("#Logs").click(function (){ 
				if(config== 1){ 
					var userpriv="false";
					var curuser="<?php echo $_SESSION["user"] ?>";
					$.get("requestdata.php", { file: 'Data/userpriv.txt' },function(data){ 
						var gdata = jQuery.parseJSON(data);
						for (var prot in gdata){
							if(gdata[prot].user=="<?php echo $_SESSION["user"] ?>") {
								userpriv=gdata[prot].Logs
							}
						};
					
						if( userpriv=="true" | curuser=="admin" ) {
						   
			//			   for (var i=0; i<logcache; i+=1) {
			//				    updatelogarea(i); 
			//					logstatus[i]=10;				
			//				}
							updatelogarea(0);
							logstatus[0]=10;
							
						    config = 0; $("h2").css("background-image","url('img/logs.png')").text("Logs"); $(".Logs").show();
						}
					});
				};
			});
			$(".finish").click(function (){ 
				for (var i=0; i<logcache; i+=1) { logstatus[i]=0 } config = 1; $(".SS").hide(); $(".Logs").hide();});
	function refreshall() {
		
		$.get("requestdata3.php", { file: 'Data/currentinfo2.log2' }, function(data){ $("footer").text(data);});
		refreshList("GetDisklist","#Disks","Data/disklist.txt");
		if (logstatus[0] > 0) {
			
			
			var date
			
			if( $("#dater").val() == "") { 
				
				date = new Date
				$("#dater").val(date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + ("0" + (date.getDate() + 0)).slice(-2)) 
				
			} 			
			dater=Date.parse($("#dater").val())
	
			for (var i=0; i<logcache; i+=1) { 
				console.log("page:",page," logstatus:"," ",logstatus," activepage:",activepage," lastpage:",lastpage," obj:",obj)
				updatelogarea(i);
				presentlog(i);
				if(logstatus[i]==10) { 
					logstatus[i]=11; 
					reqpage=page+i; $.post("./pump.php", { req:"GetLog", name: dater+' '+reqpage+' '+$("#lines").val()+' '+"<?php echo $_SESSION["user"]; ?>"},function(){}); 
				//console.log(logstatus)
				//console.log("GetLog"+dater+' '+reqpage+' '+$("#lines").val()+' '+i)
				}
				if ( logstatus[i] > 10 ) { ; ; logstatus[i]++ }
				if ( logstatus[i] > 30 ) { 
					logstatus[i]=1;
					if (i == activepage) { 
						if (lastpage <  activepage  ) { 
							if(activepage==(logcache-1)) { obj.shift(); obj.push(""); page=page+1; activepage=activepage-1; logstatus[i]=0; logtime[i]="hkslskk"
							 } else { logstatus[i+1]=10; }
							
						}
						if ( lastpage > activepage  ) {  
							if ( page > 0 ) {obj.pop(); obj.unshift(""); page=page-1 ; activepage=activepage+1; logstatus[i] = 10; logtime[i]="hiallwek"
							}
						}
						lastpage=activepage; 
					}
				
				}
			
			updatechartarea();
			}
			
		}
	}
	function updatechartarea(){
		var chartarea = "";
		var maxy = 0;var bwmaxy = 0;var rsmaxy = 0;var wsmaxy = 0;var svctmaxy = 0;var qlenmaxy = 0; var totalio = 0;
		var miny = 1000000;var bwminy = 1000000;var rsminy = 1000000;var wsminy = 1000000;var svctminy = 1000000;var qlenminy = 1000000;
		var tm ,splitstime;
		var tm2 , tme ,splitstimee;
	
		
		var qlen=[[]];var rs=[[]];var ws=[[]];var dl=[[]];var bw=[[]];var svct=[[]];
		var seriesarr="";
		todayd=new Date();
		startd=new Date ($("#Sdatec").val()); //
		endd=new Date ($("#Edatec").val()); //
		if ( endd > todayd) { endd = todayd ; };
		newSdatec = startd.toDateString() ;
		newEdatec = endd.toDateString() ;
		if ( newSdatec == oldSdatec  && newEdatec == oldEdatec ) {  //console.log("check new log");
			if (endd => todayd) { 
				$.get("requestdate.php", { file: 'Data/ctr.log.'+datemod }, function(data){
				var objdate = jQuery.parseJSON(data);
				trafficnewtime=objdate.timey;
				});
			}

		} else {
			//console.log("will do something");
			betweend = [];
			//oldSdatec = startd.toString();
			//oldEdatec = endd.toString();
			startd.setDate(startd.getDate() + 1)
			endd.setDate(endd.getDate() + 1) 
			while (startd <= endd) {
				fixbetween=startd.toISOString().split("T")[0].split("-");
				formatfix=fixbetween[0].split("20")[1]+fixbetween[1]+fixbetween[2]
				betweend.push(formatfix);
				startd.setDate(startd.getDate() + 1);
				//console.log (betweend);
			}
		
			nufiles=betweend.length
			datemod=betweend[(nufiles-1)];
			
		  trafficnewtime="newtime"
		  traffictime="oldtime"
		}	
		
			if( traffictime == trafficnewtime ) { //console.log("traffic not changed"); 
			;} 
			else { 
				count=1;
				//console.log("traffic changed");
				if ( oldEdatec != newEdatec || oldSdatec != newSdatec ) {
				//console.log("new limits",oldEdatec,newEdatec,oldSdatec,newSdatec);
					oldSdatec = newSdatec ;
					oldEdatec = newEdatec ;
					datalogf = [];
					betweend.forEach(function(datelog){
						$.get("requestdata.php", { file: 'Data/ctr.log.'+datelog }, function(data){
							if ( count == 1 ) {
								datalogf = jQuery.parseJSON(data);
								disks=datalogf.device.length
								count=count+1;
							} else {
								tmpdatalogf = jQuery.parseJSON(data);
								if (datalogf.device.length == tmpdatalogf.device.length) {
									for ( var k in datalogf.device) {
									datalogf.device[k].stats[0].Dates.push(tmpdatalogf.device[k].stats[0].Dates[0]);
									}
								}
							}
						});
					})
				};
				//console.log ("traffic changed");
				if ( endd => todayd) {
					$.get("requestlog.php", { file: 'Data/ctr.log.'+datemod }, function(data){
						tmpdatalogf = jQuery.parseJSON(data);
						if (datalogf.device.length == tmpdatalogf.device.length) {
							for ( var k in datalogf.device) {
								datalogf.device[k].stats[0].Dates.pop();
								datalogf.device[k].stats[0].Dates.push(tmpdatalogf.device[k].stats[0].Dates[0]);
							}
						}
					});
				}

				traffictime=trafficnewtime; 
				
					var device = $("#Disks").val();
					var deviceobj=[];
					deviceobj= $.grep(datalogf.device,function(e){return e.name==device});
					
					for (var k in deviceobj[0].stats[0].Dates) { 
						for (var y in deviceobj[0].stats[0].Dates[k].times) {
							//console.log(deviceobj[0].stats[0].Dates[k].times[y]);	
							 tm=new Date ($("#Sdatec").val()); //console.log("pre",tm);
							 stime=$("#Stimec").val(); splitstime=stime.split(":")
							 tm.setHours(splitstime[0],splitstime[1],0);
							 tme=new Date($("#Edatec").val());
							 stimee="23:59"; splitstimee=stimee.split(":")
							 tme.setHours(splitstimee[0],splitstimee[1],0);
							 tm2= new Date (deviceobj[0].stats[0].Dates[k].Date+" "+deviceobj[0].stats[0].Dates[k].times[y].time);
							 //console.log("post",tm); 
							
							if((new Date(tm) < new Date(tm2)) && (new Date(tme) > new Date(deviceobj[0].stats[0].Dates[k].Date)) ) {
									//console.log(k,y,tm2,deviceobj[0].stats[0].Dates[k].times[y].bw );
									bw[0].push([tm2, deviceobj[0].stats[0].Dates[k].times[y].bw]);
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].bw) > bwmaxy ) { bwmaxy = Number(deviceobj[0].stats[0].Dates[k].times[y].bw);}
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].bw) < bwminy ) { bwminy = Number(deviceobj[0].stats[0].Dates[k].times[y].bw);}
									
									//dl[0].push([y,y]);
									//console.log(k,y,tm2,deviceobj[0].stats[0].Dates[k].times[y].rs );
									rs[0].push([tm2, deviceobj[0].stats[0].Dates[k].times[y].rs]);
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].rs) > rsmaxy ) { rsmaxy = Number(deviceobj[0].stats[0].Dates[k].times[y].rs);}
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].rs) < rsminy ) { rsminy = Number(deviceobj[0].stats[0].Dates[k].times[y].rs);}
									//dl[0].push([y,y]);
							
									//console.log(k,y,tm2,deviceobj[0].stats[0].Dates[k].times[y].ws );
									ws[0].push([tm2, deviceobj[0].stats[0].Dates[k].times[y].ws]);
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].ws) > wsmaxy ) { wsmaxy = Number(deviceobj[0].stats[0].Dates[k].times[y].ws);}
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].ws) < wsminy ) { wsminy = Number(deviceobj[0].stats[0].Dates[k].times[y].ws);}
									//dl[0].push([y,y]);
						
									//console.log(k,y,tm2,deviceobj[0].stats[0].Dates[k].times[y].svct );
									svct[0].push([tm2, deviceobj[0].stats[0].Dates[k].times[y].svct]);
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].svct) > svctmaxy ) { svctmaxy = Number(deviceobj[0].stats[0].Dates[k].times[y].svct);}
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].svct) < svctminy ) { svctminy = Number(deviceobj[0].stats[0].Dates[k].times[y].svct);}
									//dl[0].push([y,y]);
									//console.log(k,y,tm2,deviceobj[0].stats[0].Dates[k].times[y].qlen );
									qlen[0].push([tm2, deviceobj[0].stats[0].Dates[k].times[y].qlen]);
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].qlen) > qlenmaxy ) { qlenmaxy = Number(deviceobj[0].stats[0].Dates[k].times[y].qlen);}
									if ( Number(deviceobj[0].stats[0].Dates[k].times[y].qlen) < qlenminy ) {qlenminy = Number(deviceobj[0].stats[0].Dates[k].times[y].qlen);}
									//dl[0].push([y,y]);

									totalio= Number(deviceobj[0].stats[0].Dates[k].times[y].rs ) + Number (deviceobj[0].stats[0].Dates[k].times[y].ws);
									if ( totalio > maxy ) { maxy = totalio;}
									if ( totalio < miny ) { miny = totalio;}
									dl[0].push([tm2,totalio]);
									
							};
						};
					};
					if(plotflag > 0) { plotbw.destroy();plotrs.destroy();plotws.destroy();plotsvct.destroy();plotqlen.destroy();plotdl.destroy(); };
					//plotbw.destroy();
				
					bwmaxy = Number(bwmaxy+1); bwminy = Number(bwminy -1);rsmaxy = Number(rsmaxy+1); rsminy = Number(rsminy -1);
					wsmaxy = Number(wsmaxy+1); wsminy = Number(wsminy -1);svctmaxy = Number(svctmaxy+1); svctminy = Number(svctminy -1);
					qlenmaxy = Number(qlenmaxy+1); qlenminy = Number(qlenminy -1);
				//	plotbw.destroy();
					var sercolr="#455B5B";
					//console.log("plotting");
					plotbw = $.jqplot('bwchart',[bw[0]], {
						title: "Bandwidth",
						seriesDefaults: {
						showMarker:false},axes: {yaxis: {min:bwminy ,max:bwmaxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,//tickOptions:{formatString:'%b %#d, %#H:%#M'}}},
						tickOptions:{formatString:'%#H:%#M'}, min:tm }},
						series: [
								{
										color: sercolr,
									//	negativeColor: 'rgba(100,50,50,.6)',
										showMarker: false,
										showLine: true,
										fill: false,
										fillAndStroke: false,
										markerOptions: {
												style: 'filledCircle',
												size: 8
										},
										rendererOptions: {
												smooth: true
										}
								}]
					});
					plotrs = $.jqplot('rschart',[rs[0]], {
						title: "Read IO/s",
						seriesDefaults: {
						showMarker:false},axes: {yaxis: {min:rsminy ,max:rsmaxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,tickOptions:{formatString:'%#H:%#M'} , min:tm }},
						series: [
								{
										color: sercolr,
										negativeColor: 'rgba(100,50,50,.6)',
										showMarker: false,
										showLine: true,
										fill: false,
										fillAndStroke: false,
										markerOptions: {
												style: 'filledCircle',
												size: 8
										},
										rendererOptions: {
												smooth: true
										}
								}]
					});
					plotws = $.jqplot('wschart',[ws[0]], {
						title: "Write IO/s",
						seriesDefaults: {
						showMarker:false},axes: {yaxis: {min:wsminy ,max:wsmaxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,//tickOptions:{formatString:'%b %#d, %#H:%#M'}}},
							tickOptions:{formatString:'%#H:%#M'}, min:tm }},
						series: [
								{
										color: sercolr,
										negativeColor: 'rgba(100,50,50,.6)',
										showMarker: false,
										showLine: true,
										fill: false,
										fillAndStroke: false,
										markerOptions: {
												style: 'filledCircle',
												size: 8
										},
										rendererOptions: {
												smooth: true
										}
								}]
					});
					plotsvct = $.jqplot('svctchart',[svct[0]], {
						title: "Latency ms",
						seriesDefaults: {
						showMarker:false},axes: {yaxis: {min:svctminy ,max:svctmaxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,tickOptions:{formatString:'%#H:%#M'}, min:tm }},
						series: [
								{
										color: sercolr,
										negativeColor: 'rgba(100,50,50,.6)',
										showMarker: false,
										showLine: true,
										fill: false,
										fillAndStroke: false,
										markerOptions: {
												style: 'filledCircle',
												size: 8
										},
										rendererOptions: {
												smooth: true
										}
								}]
					});
					plotqlen = $.jqplot('qlenchart',[qlen[0]], {
						title: "Queue length",
						seriesDefaults: {
			showMarker:false},axes: {yaxis: {min:qlenminy ,max:qlenmaxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,tickOptions:{formatString:'%#H:%#M'}, min:tm }},
			series: [
					{
							color: sercolr,
							negativeColor: 'rgba(100,50,50,.6)',
							showMarker: false,
							showLine: true,
							fill: false,
							fillAndStroke: false,
							markerOptions: {
									style: 'filledCircle',
									size: 8
							},
							rendererOptions: {
									smooth: true
							}
					}]
		});
					plotdl = $.jqplot('totaliochart',[dl[0]], {
						title: "Total IO/s",
						seriesDefaults: {
						showMarker:false},axes: {yaxis: {min:miny ,max:maxy}, xaxis:{renderer: $.jqplot.DateAxisRenderer,tickOptions:{formatString:'%#H:%#M'}, min:tm }},
						series: [
								{
							color: sercolr,
							negativeColor: 'rgba(100,50,50,.6)',
							showMarker: false,
							showLine: true,
							fill: false,
							fillAndStroke: false,
							markerOptions: {
									style: 'filledCircle',
									size: 8
							},
							rendererOptions: {
									smooth: true
							}
					}]
		});
					plotflag = 1;
				};
		
	}
	
					
			
			
	
	function updatelogarea(ii){
		
		var tm, splitstime;
		var tm2; var tme, splitstimee;
		var rqpg
		rqpg=ii+page
      
		$.get("requestdate.php", { file: 'Data/Logs.logupdated'+rqpg }, function(data){
			var objdate = jQuery.parseJSON(data);
			logtimenew=objdate.timey;
		});
		if(logtimenew!=logtime[ii] ) {
			$.get("requestdata.php", { file: 'Data/Logs.log'+rqpg}, function(data){
			console.log("Data/Logs.log"+ii+" obj ")
			obj[ii] = jQuery.parseJSON(data);
			});
		}
	}
	function presentlog(ii) {
		var logarea = "";
		config=1;
		logtime[ii]=logtimenew;
		if(lastpage!=activepage && ii==activepage) {$("#Logdetails tr.datarow").remove();}
		if( lastpage!=activepage && ii==activepage) {
				for (var k in obj[ii]) { 
						
							var objdata=obj[ii][k].data;
							var codes; var msgcode; var jofcode; var themsg; var themsgarr;
							if(typeof obj[ii][k].code != 'undefined'){
								codes=obj[ii][k].code.split("@");
								msgcode=codes[0];
								jofcode=0;
								jofcode=searchmsg(msgs,msgcode);
								//console.log("jofcode",jofcode);
								themsg=msgs[jofcode];
								themsgarr=themsg.split(":");
								codes.push(".");
								objdata=""
								for (i=1; i < themsgarr.length ;i++) {
									 objdata=objdata+themsgarr[i]+" "+codes[i]+" ";
								 }
								//console.log("codes",codes);
								//console.log("themsgarr",themsgarr);
							}
							logarea=logarea+obj[ii][k].Date+" "+obj[ii][k].time+" "+obj[ii][k].msg+": "+objdata+obj[ii][k].code+"\n";
							if(obj[ii][k].msg == "info") { color="blue"}; if(obj[ii][k].msg == "warning") { color="yellow"}; if(obj[ii][k].msg == "error") { color="red"}
							
							$("#Logdetails").append('<tr class="datarow '+obj[ii][k].msg+'" style="color:'+color+';"><td class="Volname col/-sm-3"data-toggle="popover" rel="popover" data-trigger="hover" data-container="body" data-content='+objdata+' >' +obj[ii][k].Date+' '+obj[ii][k].time+'</td><td class="col-sm-1" data-toggle="popover" rel="popover" data-trigger="hover" data-container="body" data-content='+objdata+' >'+obj[ii][k].user+'</td><td class="col-sm-7"  data-toggle="popover" rel="popover" data-trigger="hover" data-container="body" data-content='+objdata+' >'+objdata+'</td></tr>');
							
										$("#INFO").click();			$("#INFO").click();
							
						
					
				};
			}
			//$("#logsarea").val(logarea);	
			$("td").css("padding","0.1rem");
			$('[data-toggle="popover"]').popover({ placement: "bottom",html: false,
                    animation: false,});
	}
		
	

		$(".datec").datepicker().on("changeDate",function(e){
					traffictime="44:44:34";updatechartarea();											
		});
		$(".timec").change(function(){
					traffictime="44:44:34";updatechartarea();											
		});
$("#Disks").change(function(){
			traffictime="disk changes";updatechartarea();
		});
		$(".traffic").change( function () { traffictime="44:44:34"; updatechartarea();});
		$(".checkboxy").change (function(){ updatelogarea();});
		refreshList("GetDisklist","#Disks","Data/disklist.txt");
		$.post("./pump.php", { req:"GetDisklist", name: "Data/disklist.txt"},function(){});
		setInterval('refreshall()', 500); // Loop every 1000 milliseconds (i.e. 1 second)
		//console.log("<?php print $_REQUEST["idd"]; print session_id(); ?>");
		
		$('[data-toggle="popover"]').popover({
										html: true,
                    animation: false,
                    content: "TO BE ANNOUNCED",
                    placement: "bottom"
			
			
			});
			

		for (var i=0; i<logcache; i+=1) {
					logstatus[i]=0; logtime[i]="ksldl";
				}
		</script>
 
	</body>

</html>
