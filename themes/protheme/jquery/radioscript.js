$(document).ready(function(){
	$("#categorysechtml").addClass('disable');
	$('#streamsdatahtml').addClass('disable');

	// return;
	var selectedCategory = "";
	var fulldatalive = [];
	var counts = {};

	// Get Live Categories AJAX Functionaliry

	$.ajax({
        type: "POST",
        url: "includes/requesthandler.php",
        dataType:"text",
        data:{
            action:"callApiRequest",
            subaction:"get_live_categories"
        }, 
        success: function (data) {
        	if(data != "")
        	{
				fulldatalive["category"] =  JSON.parse(data);
				// cateforyhtmlemend(fulldatalive["category"],"");
			}
			// Get Live Streams AJAX Functionaliry

        	$.ajax({
		        type: "POST",
		        url: "includes/requesthandler.php",
		        dataType:"text",
		        data:{
		            action:"callApiRequest",
		            subaction:"get_live_streams"
		        }, 
		        success: function (data) {
		        	Streamshtmltpappend = "";
		        	
		        	ashbjdasbhj = [];
		        	if(data != "")
        			{
                        fulldatalive["streams"] = JSON.parse(data);
						newfilteredstreams = [];
        				categoriesidsfromstreams = [];
        				$.each(fulldatalive["streams"], function (index, value) {
        					if(value.stream_type == "radio_streams")
        					{
        						newfilteredstreams.push(value);
        						categoriesidsfromstreams[value.category_id] = value.category_id;
        					}
        				});

        				fulldatalive["streams"] = newfilteredstreams;

        				newcatetory = [];
                        $.each(fulldatalive["category"], function (index, value) {
                        	if(jQuery.inArray(value['category_id'], categoriesidsfromstreams) !== -1)
                        	{
                               	newcatetory.push(value);
                            }
                        });

                        fulldatalive["category"] = newcatetory;
                        cateforyhtmlemend(fulldatalive["category"],"");
                        

                        //Category Search Functionaliry
			
                        $('.cateSearch input').keyup(function(){
                            getcateinputvalue = $(this).val();	
                            cateforyhtmlemend(fulldatalive["category"],getcateinputvalue,counts);
                        });
                        
                        document.getElementById('removecateseach').addEventListener('input', (e) => {
                            valuesearch = e.currentTarget.value;
                            if(valuesearch == ""){
                                cateforyhtmlemend(fulldatalive["category"],"",counts);
                            }
                          })
                            document.getElementById('removestreamseach').addEventListener('input', (e) => {
                                valuesearch = e.currentTarget.value;
                                // console.log(valuesearch);
                                if(valuesearch == ""){
                                    setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
                                    alreatstreamactive = $(".liveFrameBody.active").data("streamid");
                                      nextval = $(".labelclass-"+alreatstreamactive).text();
                                      $(".labelclass-"+alreatstreamactive).html(nextval);
                                }
                              })


		        		counts["all"] = fulldatalive["streams"].length;
		        		checkcounter = 1;

		        		onloadcategoryid = "";
		        		if($(".categoryselectonload").length > 0)
		        		{
		        			onloadcategoryid = $(".categoryselectonload").data("cateid");
		        			selectedCategory = onloadcategoryid;
		        		}
		        		if(onloadcategoryid != "")
		        		{
                                console.log(fulldatalive["streams"]);

							   $.each(fulldatalive["streams"], function (index, value) {
                                console.log(value);
								//totalbycategory.push(value.category_id);
								if (!counts.hasOwnProperty(value.category_id)) {
									counts[value.category_id] = 1;
								} else {
									counts[value.category_id]++;
								}
								if(onloadcategoryid == value.category_id)
								{				        			
									activestreams = "";
									if(checkcounter == 1)
									{
										activestreams = "active";
										showHidela = "show";
										showHideprobar = "flex";
									}
									else{
										showHidela = "hide";
										showHideprobar = "hide";
									}   
									classofimage = 'imagesele-' + value.stream_id;
				        			Streamshtmltpappend+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 liveFrameBody '+activestreams+'" data-streamid="'+value.stream_id+'">';
				        			Streamshtmltpappend+= '<div class="row">';
				        			Streamshtmltpappend+= '<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
				        			Streamshtmltpappend+= '<div class="liveFrameImg">';
				        			Streamshtmltpappend+= '<img src="'+value.stream_icon+'" class="'+classofimage+'" alt="image" onerror="noposterimage(' + value.stream_id + ')">';
				        			Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '<div class="col-sm-10 col-xs-10 col-md-10 col-lg-10">';
				        			Streamshtmltpappend+= '<div class="liveFrameChanName">';
				        			Streamshtmltpappend+= '<span>'+value.name+'</span><br>';
				        			Streamshtmltpappend+= '<label class="currentProgramepg '+ showHidela +' labelclass-'+value.stream_id+'">Loading...</label>';
				        			Streamshtmltpappend+= '<div class="progress '+ showHideprobar +'">';
									Streamshtmltpappend+= '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>';
									Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '</div>';
				        			Streamshtmltpappend+= '</div>';
				        			checkcounter = Number(checkcounter)+Number(1);
			        			}
			        		});

		        		}
		        		else
		        		{
		        			$.each(fulldatalive["streams"], function (index, value) {
			        			//totalbycategory.push(value.category_id);
			        			 if (!counts.hasOwnProperty(value.category_id)) {
								    counts[value.category_id] = 1;
								  } else {
								    counts[value.category_id]++;
								  }
								activestreams = "";
								if(checkcounter == 1)
								{
									activestreams = "active";
									showHidela = "show";
									showHideprobar = "flex";
								}
								else{
									showHidela = "hide";
									showHideprobar = "hide";
								}   
								classofimage = 'imagesele-' + value.stream_id;
			        			Streamshtmltpappend+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 liveFrameBody '+activestreams+'" data-streamid="'+value.stream_id+'">';
			        			Streamshtmltpappend+= '<div class="row">';
			        			Streamshtmltpappend+= '<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
			        			Streamshtmltpappend+= '<div class="liveFrameImg">';
			        			Streamshtmltpappend+= '<img src="'+value.stream_icon+'" class="'+classofimage+'" alt="image" onerror="noposterimage(' + value.stream_id + ')">';
			        			Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '<div class="col-sm-10 col-xs-10 col-md-10 col-lg-10">';
			        			Streamshtmltpappend+= '<div class="liveFrameChanName">';
			        			Streamshtmltpappend+= '<span>'+value.name+'</span><br>';
								Streamshtmltpappend+= '<label class="currentProgramepg '+ showHidela +' labelclass-'+value.stream_id+'">No Found Data</label>';
								Streamshtmltpappend+= '<div class="progress '+ showHideprobar +'">';
								Streamshtmltpappend+= '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>';
								Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '</div>';
			        			Streamshtmltpappend+= '</div>';
			        			checkcounter = Number(checkcounter)+Number(1);
			        		});
		        		}

		        		$("#categorysechtml").removeClass('disable');
						$('#streamsdatahtml').removeClass('disable');
		        	}
		        	else
		        	{
		        		Streamshtmltpappend = "<center class='notfoundepg'> No Streams Found! </center>";
		        	}



		        	//This function will add counts to cateory
		        	addcategorycounters(counts);



		        	$("#streamsdatahtml").html(Streamshtmltpappend);


					onlaodstreamidforepg = $(".liveFrameBody.active").data("streamid");
		        	playchannesandconfigEPG(onlaodstreamidforepg);

					$(".liveFrameBody").click(function(){
						$(".liveFrameBody").removeClass("active");
						$(this).addClass("active");
						getstreamid = $(this).data("streamid");
						 playchannesandconfigEPG(getstreamid);
						$(this).find('.liveFrameChanName label').removeClass('hide');
						$(this).find('.liveFrameChanName label').addClass('show');
						$(this).find('.progress').removeClass('hide');
						$(this).find('.progress').addClass('flex');
						
					});
					
		        

		


					var typingTimer;                //timer identifier
					var doneTypingInterval = 1000;  //time in ms (5 seconds)

					//on keyup, start the countdown
					$('.SearchStreams').keyup(function(){
					    clearTimeout(typingTimer);
					    if ($('.SearchStreams').val()) {
					        typingTimer = setTimeout(doneTyping, doneTypingInterval);
					    }
					});
					var alreatstreamactive = $(".liveFrameBody.active").data("streamid");
					var nextval = $(".labelclass-"+alreatstreamactive).text();
					//user is "finished typing," do something
					function doneTyping () {
					  	searchvalusis = $(".SearchStreams").val();	
					  	categoryid = $(".categoryselect.active").data("cateid");
					  	if($(".liveFrameBody.active").length > 0)
					  	{
					  		alreatstreamactive = $(".liveFrameBody.active").data("streamid");
					  		nextval = $(".labelclass-"+alreatstreamactive).text();
					  	}
					  	setTimeout(function(){ filterstreamsbyid(categoryid,searchvalusis,alreatstreamactive); }, 100);
					  	// alert(nextval);
					  	$(".labelclass-"+alreatstreamactive).html(nextval);
					}






		        }
		    }); 
        }
    }); 

	// Show Streams all And Active Functionaliry

	function filterstreamsbyid(categoryid = "all",searchvalue = "",alreatstreamactive = "")
	{
		newStreamshtmltpappend = "";
		checkcounter = 1;
		$.each(fulldatalive["streams"], function (index, value) {
			if(searchvalue != "")
			{
				filter = searchvalue.toUpperCase();
				if (value.name.toUpperCase().indexOf(filter) > -1) 
				{
					newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend,"yes",alreatstreamactive);
				}
			}
			else
			{
				newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend);
			}
			
		});

		if(newStreamshtmltpappend != "")
		{
			$("#streamsdatahtml").html(newStreamshtmltpappend);
		}
		else
		{
			// white-no-data.gif
			$("#streamsdatahtml").html("<center class='notfoundcate'>'"+ searchvalue +"' related result not found! </center>");
		}

		$(".liveFrameBody").click(function(){
			$(".liveFrameBody").removeClass("active");
			$(this).addClass("active");
			getstreamid = $(this).data("streamid");
			playchannesandconfigEPG(getstreamid);
			$(this).find('.liveFrameChanName label').removeClass('hide');
			$(this).find('.liveFrameChanName label').addClass('show');
			$(this).find('.progress').removeClass('hide');
			$(this).find('.progress').addClass('flex');
			
		});

		if(searchvalue == "")
		{
			cateClickstreamidforepg = $(".liveFrameBody.active").data("streamid");	 	
			playchannesandconfigEPG(cateClickstreamidforepg);
		}
	}	



	function createhtmlandreturnstrems(categoryid = "all",value,newStreamshtmltpappend,insearch = "",alreatstreamactive = "")
	{	
		returndatais = "";
		if(categoryid == "all")
		{
			activestreams = "";
			if(checkcounter == 1)
			{
				activestreams = "active";
				showHidela = "show";
				showHideprobar = "flex";
			}
			else{
				showHidela = "hide";
				showHideprobar = "hide";
			} 

			if(insearch == "yes")
			{
				activestreams = "";
				showHidela = "hide";
				showHideprobar = "hide";
				if(alreatstreamactive != "")
				{
					if(alreatstreamactive == value.stream_id)
					{
						activestreams = "active";
						showHidela = "show";
						showHideprobar = "flex";
					}
				}
			}

			classofimage = 'imagesele-' + value.stream_id;
			newStreamshtmltpappend+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 liveFrameBody" id="getstreamid '+activestreams+'" data-streamid="'+value.stream_id+'">';
			newStreamshtmltpappend+= '<div class="row">';
			newStreamshtmltpappend+= '<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
			newStreamshtmltpappend+= '<div class="liveFrameImg">';
			newStreamshtmltpappend+= '<img src="'+value.stream_icon+'" class="'+classofimage+'" alt="image" onerror="noposterimage(' + value.stream_id + ')">';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '<div class="col-sm-10 col-xs-10 col-md-10 col-lg-10">';
			newStreamshtmltpappend+= '<div class="liveFrameChanName">';
			newStreamshtmltpappend+= '<span>'+value.name+'</span><br>';
			newStreamshtmltpappend+= '<label class="currentProgramepg '+ showHidela +' labelclass-'+value.stream_id+'">No Program Found</label>';
			newStreamshtmltpappend+= '<div class="progress '+ showHideprobar +'">';
			newStreamshtmltpappend+= '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			checkcounter = Number(checkcounter)+Number(1);
		}
		else if(categoryid == value.category_id)
		{
			$(".ldlz").hide();	
			activestreams = "";
			if(checkcounter == 1)
			{
				activestreams = "active";
				showHidela = "show";
				showHideprobar = "flex";
			}
			else{
				showHidela = "hide";
				showHideprobar = "hide";
			}

			if(insearch == "yes")
			{
				activestreams = "";
				showHidela = "hide";
				showHideprobar = "hide";
				if(alreatstreamactive != "")
				{
					if(alreatstreamactive == value.stream_id)
					{
						activestreams = "active";
						showHidela = "show";
						showHideprobar = "flex";
					}
				}
			}

			classofimage = 'imagesele-' + value.stream_id;
			newStreamshtmltpappend+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 liveFrameBody '+activestreams+'" data-streamid="'+value.stream_id+'">';
			newStreamshtmltpappend+= '<div class="row">';
			newStreamshtmltpappend+= '<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
			newStreamshtmltpappend+= '<div class="liveFrameImg">';
			newStreamshtmltpappend+= '<img src="'+value.stream_icon+'" alt="image" class="'+classofimage+'" onerror="noposterimage(' + value.stream_id + ')">';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '<div class="col-sm-10 col-xs-10 col-md-10 col-lg-10">';
			newStreamshtmltpappend+= '<div class="liveFrameChanName">';
			newStreamshtmltpappend+= '<span>'+value.name+'</span><br>';
			newStreamshtmltpappend+= '<label class="currentProgramepg '+ showHidela +' labelclass-'+value.stream_id+'">No Program Found</label>';
			newStreamshtmltpappend+= '<div class="progress '+ showHideprobar +'">';
			newStreamshtmltpappend+= '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			newStreamshtmltpappend+= '</div>';
			checkcounter = Number(checkcounter)+Number(1);
		}
		returndatais = newStreamshtmltpappend;
		return returndatais;
	}


	// Show Streams Loader when change category Functionaliry

	function showHideLoaderCateData(){
		loaderhtml = '';
		for(var i = 1; i < 20; i++)
		{
	    loaderhtml+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 liveFrameBody onloadlive">';
		loaderhtml+= '<div class="row" >';
		loaderhtml+= '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
		loaderhtml+= '<div class="liveFrameChanName" style="text-align: center; width: 100%;">';
		loaderhtml+= '<span style="width: 100%;">';
		loaderhtml+= '<div class="linear-background">';
		loaderhtml+= '<div class="inter-draw"></div>';
		loaderhtml+= '<div class="inter-crop"></div>';
		loaderhtml+= '<div class="inter-right--top"></div>';
		loaderhtml+= '<div class="inter-right--bottom"></div>';
		loaderhtml+= '</div>';
		loaderhtml+= '</div>';
		loaderhtml+= '</div>';
		loaderhtml+= '</div>';
		}
		$("#streamsdatahtml").html(loaderhtml);
	}

	// Show EPG Loader when change Streams Functionaliry

	function showHideLoaderEPGData(){
		loaderhtml = '';
		loaderhtml = '<center class="notfoundepg">Loading EPG data</center>';
		// for(var i = 1; i < 20; i++)
		// {
	    // loaderhtml+= '<div class="liveEpgTime" style="padding: 10px 20px 0px 20px !important;">';
		// loaderhtml+= '	<div class="linear-background-epg">';
		// loaderhtml+= '		<div class="inter-draw-epg"></div>';
		// loaderhtml+= '		<div class="inter-crop-epg"></div>';
		// loaderhtml+= '		<div class="inter-right--top-epg"></div>';
		// loaderhtml+= '		<div class="inter-right--bottom-epg"></div>';
		// loaderhtml+= '	</div>';
		// loaderhtml+= '</div>';
		// }
		$(".liveEPGdiv").html(loaderhtml);
	}

	// Get EPG and Player Functionaliry

	function playchannesandconfigEPG(streamId){
	// $('.liveEPGdiv').append('<span class="notfoundcate">Loading EPG data</span>');

		showHideLoaderEPGData();
		$.ajax({
			type: "POST",
			url: "includes/requesthandler.php",
			dataType:"text",
			data:{
				action:"callApiRequestEpg",
				subaction:"get_short_epg&stream_id="+streamId
			}, 
			success: function (data) {
				
				$('.liveEPGdiv').html(data);
				$('.livePlayer img').attr('src','./images/IMG123.jpg');
				
				if($(".firstepgis-"+streamId).length > 0)
				{
					titleis = $(".firstepgis-"+streamId).data("titleis");
					$(".labelclass-"+streamId).html(titleis);
					$(".labelclass-"+streamId).removeClass("hide");
				}else{
					$(".labelclass-"+streamId).html('No Program Found');
				}
			}
		}); 
	}

	// Categories show and show hide Categories Search according Functionaliry

	function cateforyhtmlemend(allcategories = "", searchtext = "",counts = "")
	{
		alreadyselected = "";
		if($(".categoryselect.active").length > 0)
		{
			alreadyselected = $(".categoryselect.active").data("cateid");
        }
        
        var allarray = [{'category_id' : 'all', 'category_name' : 'All', 'parent_id' : '0'}];
        var favouritearray = [{'category_id' : 'favourite', 'category_name' : 'Favourite', 'parent_id' : '0'}];
        var arr3 = $.merge(allarray,favouritearray);
        var newCategoriesadded = $.merge(allarray,allcategories);


		Categoryhtmltpappend = "";
		if(searchtext != "")
		{
			searchcategoryhtmlappend = Categoryhtmltpappend;
			filter = searchtext.toUpperCase();
			$.each(newCategoriesadded, function (index, value) {
				if (value.category_name.toUpperCase().indexOf(filter) > -1) 
				{
		        	index = Number(index) + Number(1);
		            

		            searchcategoryhtmlappend += '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 categoryselect" data-toggle="tooltip" title="'+value.category_name+'" data-cateid="'+value.category_id+'">';
		            searchcategoryhtmlappend += '<div class="row cateList">';
		            searchcategoryhtmlappend += '<div class="col-sm-9 col-xs-9 col-md-9 col-lg-9" style="text-align: initial;">';
					searchcategoryhtmlappend += '<div class="liveCateList">';
		            // searchcategoryhtmlappend += 'style="padding-left: 25px !important;"';
		            searchcategoryhtmlappend += '<span>'+value.category_name+'</span>';
		            searchcategoryhtmlappend += '</div>';
		            searchcategoryhtmlappend += '</div>';
		            searchcategoryhtmlappend += '<div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 cateNum">';
		            searchcategoryhtmlappend += '<div class="totalcountare" id="totalstreams-'+value.category_id+'"><img class="ldlz" src="images/golden.svg" style="width: 30%;opacity: 1; visibility: visible;"></div>';
		            searchcategoryhtmlappend += '</div>';
		            searchcategoryhtmlappend += '</div>';
		            searchcategoryhtmlappend += '</div>';
		        }
		    });
			if(searchcategoryhtmlappend == "")
			{
				searchcategoryhtmlappend = "<center class='notfoundcate'>No Category Found</center>";
			}
			$("#categorysechtml").html(searchcategoryhtmlappend);
			addcategorycounters(counts);
		}
		else
		{

			$.each(newCategoriesadded, function (index, value) {
		        index = Number(index) + Number(1);		            	
		            onloadplaycategoryget = "";
		            if(index == 3)
		            {
		            	onloadplaycategoryget = "categoryselectonload active";
		            }

		            if(counts != "")
		            {
		            	onloadplaycategoryget = "";
		            }

		            Categoryhtmltpappend += '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 categoryselect '+onloadplaycategoryget+'" data-toggle="tooltip" title="'+value['category_name']+'" data-cateid="'+value['category_id']+'">';
		            Categoryhtmltpappend += '<div class="row cateList">';
		            Categoryhtmltpappend += '<div class="col-sm-9 col-xs-9 col-md-9 col-lg-9" style="text-align: initial;">';
		            Categoryhtmltpappend += '<div class="liveCateList">';
		            // Categoryhtmltpappend += 'style="padding-left: 25px !important;"';
		            Categoryhtmltpappend += '<span>'+value['category_name']+'</span>';
		            Categoryhtmltpappend += '</div>';
		            Categoryhtmltpappend += '</div>';
		            Categoryhtmltpappend += '<div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 cateNum">';
		            Categoryhtmltpappend += '<div class="totalcountare" id="totalstreams-'+value['category_id']+'"><img class="ldlz" src="images/golden.svg" style="width: 30%;opacity: 1; visibility: visible;"></div>';
		            Categoryhtmltpappend += '</div>';
		            Categoryhtmltpappend += '</div>';
		            Categoryhtmltpappend += '</div>';
		    });
	        	
			$("#categorysechtml").html(Categoryhtmltpappend);
			if(counts != "")
			{
				addcategorycounters(counts);
			}
		}

		$(".categoryselect").click(function(){
			showHideLoaderCateData();
				$(".categoryselect").removeClass("active");
				$(this).addClass("active");
			categoryid = $(this).data("cateid");
			selectedCategory = categoryid;
			setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
		});
		

		if(selectedCategory != "")
		{
			$(".categoryselect[data-cateid='" + selectedCategory +"']").addClass("active");
		} 
	}



	// Show categories according streams count Functionaliry

	function addcategorycounters(counts = "")
	{
		$("#totalstreams-all").html(counts["all"]);
		$("#totalstreams-favourite").html("0");
		if(counts != "" && counts != null)
		{

			$.each(counts, function (index, value) {
				$("#totalstreams-"+index).html(value);
			});
		}
	}

});

// Show Images on image error Functionaliry

function noposterimage(thisvar = "")
{
    $(".imagesele-" + thisvar).attr("src", "images/NoPostervertical.png");
}	