$(document).ready(function(){
    $("#movieCategories").addClass('disable');
    $('#getmoviesstream').addClass('disable');
    // return;

    // Movies categories AJAX funcationalty
    var selectedCategory = "";
    var fulldatalive = [];
    var counts = {};

    $.ajax({
        type: "POST",
        url: "includes/requesthandler.php",
        dataType:"text",
        data:{
            action:"callApiRequest",
            subaction:"get_vod_categories"
        }, 
        success: function (data) {
            if(data != "")
        	{
               fulldatalive["category"] =  JSON.parse(data);
               cateforyhtmlemend(fulldatalive["category"],"");
            }

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


			// $("#removecateseach").click(function(){
			// 	cateforyhtmlemend(fulldatalive["category"],"",counts);
            // });
            
            document.getElementById('removestreamseach').addEventListener('input', (e) => {
                valuesearch = e.currentTarget.value;
                if(valuesearch == ""){
                    setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
                    alreatstreamactive = $(".liveFrameBody.active").data("streamid");
                    nextval = $(".labelclass-"+alreatstreamactive).text();
                    $(".labelclass-"+alreatstreamactive).html(nextval);
                }
              })

              $('.closeSearch').click(function(){
                $('.SearchStreams').val('');
                valuesearch = $('.SearchStreams').val();
                if(valuesearch == ""){
                    setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
                    alreatstreamactive = $(".liveFrameBody.active").data("streamid");
                    nextval = $(".labelclass-"+alreatstreamactive).text();
                    $(".labelclass-"+alreatstreamactive).html(nextval);
                }
            })
        
            // Get Live Streams AJAX Functionaliry

            $.ajax({
                type: "POST",
                url: "includes/requesthandler.php",
                dataType:"text",
                data:{
                    action:"callApiRequest",
                    subaction:"get_vod_streams"
                }, 
                success: function (data) {
                    moviestreamhtmlappend = "";
                    
                    ashbjdasbhj = [];
                    if(data != "")
                    {
                        fulldatalive["streams"] = JSON.parse(data);
                        //$(".totalcountare").html("0000");
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
                            var movieStreamReadCountr = 103;
                            $.each(fulldatalive["streams"], function (index, value) {
                                //totalbycategory.push(value.category_id);
                                if (!counts.hasOwnProperty(value.category_id)) {
                                    counts[value.category_id] = 1;
                                } else {
                                    counts[value.category_id]++;
                                }

                                if(onloadcategoryid == "all")
                                {
                                    activestreams = "";
                                    if(checkcounter == 1)
                                    {
                                        activestreams = "active";
                                    }  
                                    if(checkcounter < movieStreamReadCountr)
                                    {
                                        moviestreamhtmlappend+= onlyhtmlembedstreams(value,checkcounter);
                                    }
                                    checkcounter = Number(checkcounter)+Number(1);                                
                                }
                                else if(onloadcategoryid == value.category_id)
                                {                                   
                                    activestreams = "";
                                    if(checkcounter == 1)
                                    {
                                        activestreams = "active";
                                    }  
                                    if(checkcounter < 103)
                                    {
                                        moviestreamhtmlappend+= onlyhtmlembedstreams(value,checkcounter);
                                    }
                                    checkcounter = Number(checkcounter)+Number(1);
                                }
                            });


                            if(checkcounter <= 100){

                            }else{
                                moviestreamhtmlappend+='<div class="buttons-container centered" id="loadDataappend">';
                                moviestreamhtmlappend+='<button id="readMstream" data-offset="0" data-limit="101" class="button btn-secondary built-for-cta mobile w-button" style=" font-weight: 500; padding: 6px 20px !important; margin-bottom: 20px !IMPORTANT; border-radius: 6px; ">';
                                moviestreamhtmlappend+='Read More... <i id="loadingreader" style="display:none;" class="fa fa-spin fa-spinner" aria-hidden="true"></i>';
                                moviestreamhtmlappend+='</button>';
                                moviestreamhtmlappend+='</div>';
                            }

                                    // moviestreamhtmlappend+='<div class="buttons-container centered">';
                                    // moviestreamhtmlappend+='<div class="button secondary built-for-cta mobile w-button">Read More...</div>';
                                    // moviestreamhtmlappend+='</div>';
                                    // moviestreamhtmlappend+='<div class="buttons-container centered" id="loadDataappend">';
                                    // moviestreamhtmlappend+='<button id="readMstream" data-offset="0" data-limit="101" class="button btn-secondary built-for-cta mobile w-button" style=" font-weight: 500; padding: 6px 20px !important; margin-bottom: 20px !IMPORTANT; border-radius: 6px; ">';
                                    // moviestreamhtmlappend+='Read More... <i id="loadingreader" style="display:none;" class="fa fa-spin fa-spinner" aria-hidden="true"></i>';
                                    // moviestreamhtmlappend+='</button>';
                                    // moviestreamhtmlappend+='</div>';

                        }
                    }
                    else
                    {
                        moviestreamhtmlappend = "<center class='notfoundepg'> No Streams Found! </center>";
                    }
                        


                    //This function will add counts to cateory
                    addcategorycounters(counts);



                    if(moviestreamhtmlappend != ""){
                        $('#getmoviesstream').html(moviestreamhtmlappend);
                    }else{
                        $('#getmoviesstream').html('<center class="notfoundepg"> No Streams Found! </center>');
                    }

                    headername = $('.categoryselect.active').data('toggle');
                    $('.liveHead span').html(headername);

                    $('.mainsss').click(function(){
                        movieinfotoprowmaterialdataappend();
                        $('#movieMain').removeClass('show');
                        $('#movieMain').addClass('hide');
                        $('#movieInfo').removeClass('hide');
                        $('#movieInfo').addClass('show');
                        getvodid = $(this).data('vodid');
                        movieinfogetdata(getvodid);
                    });
                    $('.getmovieInfo').click(function(){
                        $('#movieMain').removeClass('hide');
                        $('#movieMain').addClass('show');
                        $('#movieInfo').removeClass('show');
                        $('#movieInfo').addClass('hide');
                    });

                    var typingTimer;                //timer identifier
					var doneTypingInterval = 1000;

                    $('.SearchStreams').keyup(function(){
					    clearTimeout(typingTimer);
					    if ($('.SearchStreams').val()) {
					        typingTimer = setTimeout(doneTyping, doneTypingInterval);
					    }
					});
					//user is "finished typing," do something
					function doneTyping () {
                        searchvalusis = $(".SearchStreams").val();
					  	categoryid = $(".categoryselect.active").data("cateid");
					  	setTimeout(function(){ filterstreamsbyid(categoryid,searchvalusis); }, 100);
					}

                    // $('#readMstream').on('click', function(){
                    //     $('#loadingreader').show();
                    //     var getOffset = $('#readMstream').data('offset');
                    //     var getLimit = $('#readMstream').data('limit');
            
                    //     var cateId = $('.categoryselect.active').data('cateid');
                    //     var getRcount = parseInt(getOffset) + 102; 
                    //     var dataoffset = getRcount;
                    //     var datalimit = parseInt(getLimit) + 102;
                    //     setTimeout(function(){ loadreaderCount(cateId,dataoffset,datalimit); }, 100);

                    //     // loadreaderCount(cateId,dataoffset,datalimit);
                       
                    //     $('#readMstream').data("offset",dataoffset);
                    //     $('#readMstream').data("limit",datalimit);
                    // });
                }
            }); 

        }

    }); 

    function filterstreamsbyid(categoryid = "all",searchvalue = "",dataoffset = 0,datalimit = 101)
    {
        $(".live_body").animate({ scrollTop: 0 }, "fast");
        newStreamshtmltpappend = "";
        checkcounter = 0;
        $.each(fulldatalive["streams"], function (index, value) {
			if(searchvalue != "")
			{
				filter = searchvalue.toUpperCase();
				if (value.name.toUpperCase().indexOf(filter) > -1) 
				{
					newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend);
				}
                // else
                // {
                //     newStreamshtmltpappend = "<center class='notfoundepg'>'"+ searchvalue +"' related result not found! </center>";
                // }
			}
			else
			{
                if(checkcounter >= dataoffset && checkcounter <= datalimit){
				    newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend);
                }
			}
			// checkcounter = Number(checkcounter)+Number(1);
		});


		if(newStreamshtmltpappend != "")
		{
            checksdre = $('.categoryselect.active').find('.totalcountare').text();
            if(checkcounter <= 100){
                $('#getmoviesstream').find('#loadDataappend').hide();
            }else if(checkcounter == checksdre){
                $('#getmoviesstream').find('#loadDataappend').hide();
            }
            else{
                newStreamshtmltpappend+='<div class="buttons-container centered" id="loadDataappend">';
                newStreamshtmltpappend+='<button id="readMstream" data-offset="0" data-limit="101" class="button btn-secondary built-for-cta mobile w-button" style=" font-weight: 500; padding: 6px 20px !important; margin-bottom: 20px !IMPORTANT; border-radius: 6px; ">';
                newStreamshtmltpappend+='Read More... <i id="loadingreader" style="display:none;" class="fa fa-spin fa-spinner" aria-hidden="true"></i>';
                newStreamshtmltpappend+='</button>';
                newStreamshtmltpappend+='</div>';
            }

			$("#getmoviesstream").html(newStreamshtmltpappend);
		}
		else
		{
            $('#getmoviesstream').find('#loadDataappend').hide();
			$("#getmoviesstream").html("<center class='notfoundepg'>Result not found! </center>");
			// $("#getmoviesstream").html('<img class="ldlz" src="images/white-no-data.gif" style="width: 30%;opacity: 1; visibility: visible;">');
		}

        $('.mainsss').click(function(){
            movieinfotoprowmaterialdataappend();
            $('#movieMain').removeClass('show');
            $('#movieMain').addClass('hide');
            $('#movieInfo').removeClass('hide');
            $('#movieInfo').addClass('show');
            getvodid = $(this).data('vodid');
            movieinfogetdata(getvodid);
        });
        $('.getmovieInfo').click(function(){
            $('#movieMain').removeClass('hide');
            $('#movieMain').addClass('show');
            $('#movieInfo').removeClass('show');
            $('#movieInfo').addClass('hide');
        });

            
        $('#readMstream').on('click', function(){
            $('#loadingreader').show();
            var getOffset = $('#readMstream').data('offset');
            var getLimit = $('#readMstream').data('limit');

            var cateId = $('.categoryselect.active').data('cateid');
            var getRcount = parseInt(getOffset) + 102; 
            var dataoffset = getRcount;
            var datalimit = parseInt(getLimit) + 102;
            setTimeout(function(){ loadreaderCount(cateId,dataoffset,datalimit); }, 100);

            // loadreaderCount(cateId,dataoffset,datalimit);
           
            $('#readMstream').data("offset",dataoffset);
            $('#readMstream').data("limit",datalimit);
        });

    }   

    function createhtmlandreturnstrems(categoryid = "all",value,newStreamshtmltpappend,dataoffset = 0,datalimit = 101)
	{	
		returndatais = "";
		if(categoryid == "all")
            {

                // $('.liveHead span').html(categoryid);
                
                activestreams = "";
                if(checkcounter == 1)
                {
                    activestreams = "active";
                }  

                if(checkcounter >= dataoffset && checkcounter <= datalimit){
                    newStreamshtmltpappend+= onlyhtmlembedstreams(value,checkcounter);
                }                
                checkcounter = Number(checkcounter)+Number(1);
            }
            else if(categoryid == value.category_id)
            {
                // headername = $('.categoryselect.active').data('toggle');
                // // alert(headername);
                // $('.liveHead span').html(headername);


                $(".ldlz").hide();  
                activestreams = "";
                if(checkcounter == 1)
                {
                    activestreams = "active";
                } 
                if(checkcounter >= dataoffset && checkcounter <= datalimit){
                    newStreamshtmltpappend+= onlyhtmlembedstreams(value,checkcounter);
                }                    
                checkcounter = Number(checkcounter)+Number(1);   
            }
		returndatais = newStreamshtmltpappend;
		return returndatais;
	}

    

    function cateforyhtmlemend(allcategories = "", searchtext = "",counts = "")
    {

        alreadyselected = "";
        if($(".categoryselect.active").length > 0)
        {
            alreadyselected = $(".categoryselect.active").data("cateid");
        }

        movieCatehtmlappend = "";
        if(searchtext != "")
        {
            searchcategoryhtmlappend = movieCatehtmlappend;
            filter = searchtext.toUpperCase();
            $.each(allcategories, function (index, value) {
                if (value.category_name.toUpperCase().indexOf(filter) > -1) 
                {
                    index = Number(index) + Number(1);
                    searchcategoryhtmlappend+= onlyhtmlembedcategories(value,"");
                }
            });
            if(searchcategoryhtmlappend == "")
            {
                searchcategoryhtmlappend = "<center class='notfoundcate'> No Category Found! </center>";
            }
            $("#movieCategories").html(searchcategoryhtmlappend);
            addcategorycounters(counts);
        }
        else
        {
            $.each(allcategories, function (index, value) {
                index = Number(index) + Number(1);
                        
                    onloadplaycategoryget = "";
                    if(index == 4)
                    {
                        onloadplaycategoryget = "categoryselectonload active";
                    }

                    if(counts != "")
                    {
                        onloadplaycategoryget = "";
                    }

                    movieCatehtmlappend+= onlyhtmlembedcategories(value,onloadplaycategoryget);
            });
                
            $('#movieCategories').html(movieCatehtmlappend);
            if(counts != "")
            {
                addcategorycounters(counts);
            }
        }

        $(".categoryselect").click(function(){
            headername = $(this).data('toggle');
            $('.liveHead span').html(headername);
           /* alert("00001");*/
            showHideLoaderCateData();
                $(".categoryselect").removeClass("active");
                $(this).addClass("active");
            categoryid = $(this).data("cateid");
            selectedCategory = categoryid;
            setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
            $('.centerSearch').hide();
            $('.livecenterSearch').hide();
            $('.liveHead span').show();
            $('.searchBar').removeAttr("style");
            $('.liveInsearch').show();
            $('.liveback').show();
            $('.div6').show();
            $('.div1').attr("class","col-sm-1 col-xs-1 col-md-1 col-lg-1 div1");
            $('.SearchStreams').val('');
        });
        

        if(selectedCategory != "")
        {
            $(".categoryselect[data-cateid='" + selectedCategory +"']").addClass("active");
        } 
    
    }


    function addcategorycounters(counts = "")
    {
        if(counts != "")
        {
           $("#totalstreams-all").html(counts["all"]);
            $("#totalstreams-favourite").html("0");
            $("#totalstreams-continue-watching").html("0");
            if(counts != "" && counts != null)
            {

                $.each(counts, function (index, value) {
                    $("#totalstreams-"+index).html(value);
                });
            } 
        }
    }

    function showHideLoaderCateData(){
        /*alert("loader on images dilawar");*/
    }



    function onlyhtmlembedstreams(value = "",listnumber = "")
    {
        returndata = "";
        if(value != "")
        {
            hideclassrating = "";
            if(!$.isNumeric(value.rating)){
                hideclassrating = "hideratibng";
            }
            


            classofimage = 'imagesele-' + value.stream_id;
            returndata+='<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2 streamselector posterDiv padding sectabno-'+listnumber+'" data-streamid="'+ value.stream_id +'" data-listnumber="'+ listnumber +'">';
            returndata+='    <div class="liveShowImages">';
            returndata+='        <span class="hoverOvers '+hideclassrating+'">'+ value.rating +'</span>';
            returndata+='        <span class="hoverOver" title="'+ value.name +'">'+ value.name +'</span>';
            returndata+='        <span title="'+ value.name +'">';
            returndata+='            <img class="mainsss '+classofimage+'" src="'+ value.stream_icon +'" alt="'+ value.name +'" data-vodid="'+ value.stream_id +'" onerror="noposterimage(' + value.stream_id + ')">';
            returndata+='        </span>';
            returndata+='    </div>';
            returndata+='</div>';
        }

        $("#movieCategories").removeClass('disable');
	    $('#getmoviesstream').removeClass('disable');
        return returndata;
    }


    function onlyhtmlembedcategories(value = "",onloadplaycategoryget = "")
    {
        returndata = "";
        if(value != "")
        {
            returndata += '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 categoryselect '+onloadplaycategoryget+'" data-toggle="'+value.category_name+'" title="'+value.category_name+'" data-recounts="101" data-cateid="'+value.category_id+'">';
            returndata += '<div class="row cateList">';
            returndata += '<div class="col-sm-9 col-xs-9 col-md-9 col-lg-9 padding" style="text-align: initial;">';
            returndata += '<div class="liveCateList">';
            returndata += '<span>'+value.category_name+'</span>';
            returndata += '</div>';
            returndata += '</div>';
            returndata += '<div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 cateNum padding">';
            returndata += '<div class="totalcountare" id="totalstreams-'+value.category_id+'"><img class="ldlz" src="images/golden.svg" style="width: 30%;opacity: 1; visibility: visible;"></div>';
            returndata += '</div>';
            returndata += '</div>';
            returndata += '</div>';
        }
        return returndata;
    }


    function streamsmanagenow(Start = 0, end = 52,lastloaded = 0)
    {
      if(fulldatalive["streams"] != "")
      {
        moviestreamhtmlappend = "";           
        end = Number(Start)+Number(end); 
        $.each(fulldatalive["streams"], function( index, value ) {
          if(selectedCategory == "all")
          {
                index = Number(index)+Number(1);
                if(index >= Start && index <= end)
                {
                    moviestreamhtmlappend+= onlyhtmlembedstreams(value,index);
                }
          }
          else if(selectedCategory == value.category_id)
          {
                index = Number(index)+Number(1);
                if(index >= Start && index <= end)
                {
                    moviestreamhtmlappend+= onlyhtmlembedstreams(value,index);
                }
          }
          
        });
        $(".sectabno-"+lastloaded).after(moviestreamhtmlappend);
      }     
    }
    var getRcount = 101;//$('.categoryselect.active').data('recounts');

    function loadreaderCount(categoryid = "all",dataoffset = 0,datalimit = 100) {
        newStreamshtmltpappend = "";
        checkcounter = 0;
        $.each(fulldatalive["streams"], function (index, value) {
            
			    newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend,dataoffset,datalimit);
            
			// checkcounter = Number(checkcounter)+Number(1);
		});

		if(newStreamshtmltpappend != "")
		{
            checksdre = $('.categoryselect.active').find('.totalcountare').text();
            $('#loadingreader').hide();
            $( "#loadDataappend" ).before(newStreamshtmltpappend);
            if(datalimit >= checksdre){
                setTimeout(function(){ $('#getmoviesstream').find('#loadDataappend').hide(); }, 1000);
            }
		}
		else
		{
            $('#loadingreader').hide();
		}

        $('.mainsss').click(function(){
            movieinfotoprowmaterialdataappend();
            $('#movieMain').removeClass('show');
            $('#movieMain').addClass('hide');
            $('#movieInfo').removeClass('hide');
            $('#movieInfo').addClass('show');
            getvodid = $(this).data('vodid');
            movieinfogetdata(getvodid);
        });
        $('.getmovieInfo').click(function(){
            $('#movieMain').removeClass('hide');
            $('#movieMain').addClass('show');
            $('#movieInfo').removeClass('show');
            $('#movieInfo').addClass('hide');
        });
        // $('#readMstream').on('click', function(){
        //     loadreaderCount();
        // });

    }   



});
function noposterimage(thisvar = "")
{
    $(".imagesele-" + thisvar).attr("src", "images/NoPoster.png");
}

function movieinfogetdata(streamId){

    $('.hiloader').removeClass('hide');
    $('.hiloader').addClass('show');

    $.ajax({
        type: "POST",
        url: "includes/requesthandler.php",
        dataType:"text",
        data:{
            action:"callApiRequest",
            subaction:"get_vod_info&vod_id="+streamId
        }, 
        success: function (data) {

            if(data != "")
            {
                $('.hiloader').removeClass('show');
                $('.hiloader').addClass('hide');
                moviefullinfo = JSON.parse(data);
                castid = moviefullinfo['info'].tmdb_id;
                movieinfotopdataappend(moviefullinfo); 
            }

                $.ajax({
                    type: "POST",
                    url: "includes/requesthandler.php",
                    dataType:"text",
                    data:{
                        action:"callApiForCastRequest",
                        subaction:castid
                    }, 
                    success: function (data) {
                        
                        if(data != "")
                        {
                            movisecastinfohtml = "";
                            moviefullcastinfo = JSON.parse(data);
                            castinfo = moviefullcastinfo['credits']['cast'];
                            $.each(castinfo, function (index, value) {
                                movisecastinfohtml += moviecastinfohtmlappend(value);
                            });

                            $('#getcastinfo').html(movisecastinfohtml);
    
                        }
            
                        $('.movieperinfo').click(function(){
                            seriesperpersonnulldataappend();
                            $('#movieInfo').removeClass('show');
                            $('#movieInfo').addClass('hide');
                            $('#movieCastInfo').removeClass('hide');
                            $('#movieCastInfo').addClass('show');
                            personId = $(this).data('personid');
                            getpersonInfo(personId);
                        });
            
                    }
                });

        }
    });

}

function movieinfotopdataappend(data){

    $('.movieInName span').html(data['info'].name);

    movieinfodataappend = "";
    favbtn = '';
    favactions = 'add';

    movieinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBody">';
    movieinfodataappend+='     <div class="row">';
    movieinfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
    movieinfodataappend+='     <div class="movieInPic">';
    movieinfodataappend+='         <img src="'+ data['info'].movie_image +'" alt="2020"><br>';
    movieinfodataappend+='         <i class="fa fa-star" aria-hidden="true"></i>';
    movieinfodataappend+='         <i class="fa fa-star" aria-hidden="true"></i>';
    movieinfodataappend+='         <i class="fa fa-star-half-o" aria-hidden="true"></i>';
    movieinfodataappend+='         <i class="fa fa-star" aria-hidden="true" style="color: #4d4a4a;"></i>';
    movieinfodataappend+='         <i class="fa fa-star" aria-hidden="true" style="color: #4d4a4a;"></i>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+='     <button class="hide trailerWatcher trailerWatcherClose" style="background: #b4b4b42e; color: white; border: none; border-radius: 6px; font-size: 18px; margin-top: 12px !important; padding: 8px 15px !important;">Back Info</button>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+='<div class="col-sm-10 col-xs-10 col-md-10 col-lg-10 hide trailerWatcher">'
    movieinfodataappend+='   <div class="trailerWatcherClose" style="position: relative;background: white;color: black;font-size: 23px;font-weight: 700;cursor: pointer;border-radius: 50px;width: 36px;height: 36px;left: 49.8%;top: -2%;"> X </div>'
    movieinfodataappend+='   <div style="border: 6px solid #ffffff; margin-top: -35px !important;" class="plyr__video-embed" id = "player">'
    movieinfodataappend+='       <iframe id="theVideo" style="width: 100%; height: 590px;" src="https://www.youtube.com/embed/' + data['info'].youtube_trailer + '?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
    movieinfodataappend+='   </div>'
    movieinfodataappend+='</div>'
    // movieinfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2 hide trailerWatcher">';
    // movieinfodataappend+=' </div>';
    movieinfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2 show fortrialer">';
    movieinfodataappend+='    <div class="movieInInformation">';
    movieinfodataappend+='         <div class="one"> ';
    movieinfodataappend+='             Directed By:';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='        <div class="one">';
    movieinfodataappend+='           Release Data:';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="one">';
    movieinfodataappend+='             Duration:';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="one">';
    movieinfodataappend+='             Genre:';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="one">';
    movieinfodataappend+='             Cast:';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+=' <div class="col-sm-7 col-xs-7 col-md-7 col-lg-7 show fortrialer">';
    movieinfodataappend+='     <div class="movieInInformation">';
    movieinfodataappend+='         <div class="two">';
    if(data['info'].director){
        movieinfodataappend+='            '+ data['info'].director +' ';
    }else{
        movieinfodataappend+='            N/A ';
    }
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="two">';
    if(data['info'].releasedate){
        movieinfodataappend+='            '+ data['info'].releasedate +' ';
    }else{
        movieinfodataappend+='            N/A ';
    }
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="two">';
    if(data['info'].duration){
        movieinfodataappend+='            '+ data['info'].duration +' ';
    }else{
        movieinfodataappend+='            N/A ';
    }
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="two">';
    if(data['info'].genre){
        movieinfodataappend+='            '+ data['info'].genre +' ';
    }else{
        movieinfodataappend+='            N/A ';
    }
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="two">';
    if(data['info'].cast){
        movieinfodataappend+='             '+ data['info'].cast +'';
    }else{
        movieinfodataappend+='             N/A';
    }
    movieinfodataappend+='         </div>';
    movieinfodataappend+='         <div class="three">';
    movieinfodataappend+='             <button data-streamid="'+ data['movie_data'].stream_id +'">Play</button>';
    movieinfodataappend+='             <button class="trailerWatcherShow"  data-trailerUrl="'+ data['info'].youtube_trailer +'">Watch Trailer</button>';
    // onclick="trailerWatcherShow(this)"
    movieinfodataappend+='         </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+=' <div class="col-sm-1 col-xs-1 col-md-1 col-lg-1 show fortrialer">';
    movieinfodataappend+='     <div class="movieInFav">';
    movieinfodataappend+='         <div class="star">';
    movieinfodataappend+='             <i class="fa fa-heart addfavstream" '+favbtn +' data-favstreamid="'+data['movie_data'].stream_id+'" data-favaction="'+favactions+'" aria-hidden="true"></i>';
    // movieinfodataappend+='             <img src="images/heart.png" alt="2020" onclick="addtofav(this)" data-favsection="movies" data-favstreamid="'+ data['movie_data'].stream_id +'" data-favaction="add">';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    movieinfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    movieinfodataappend+='         <div class="row">';
    movieinfodataappend+='             <div class="movieInDescription">';
    movieinfodataappend+='             '+ data['info'].description +'';
    movieinfodataappend+='             </div>';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';
    movieinfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    movieinfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInStarCastScroll">';
    movieinfodataappend+='         <div class="row" id="getcastinfo">';
    movieinfodataappend+='             </div>';
    movieinfodataappend+='         </div>';
    movieinfodataappend+='     </div>';
    movieinfodataappend+=' </div>';

        // return movieinfodataappend;
        $('#topartget').html(movieinfodataappend);

        iframeTag = document.querySelector("iframe"),
        // Retrieve window object needed for postMessage
        win = iframeTag.contentWindow;

        $('.trailerWatcherShow').click(function(){
                $('.trailerWatcher').removeClass('hide');
                $('.fortrialer').removeClass('show');
                $('.trailerWatcher').addClass('show');
                $('.fortrialer').addClass('hide');
        });

        $('.trailerWatcherClose').click(function(){
            $('.trailerWatcher').removeClass('show');
            $('.trailerWatcher').addClass('hide');
            $('.fortrialer').removeClass('hide');
            $('.fortrialer').addClass('show'); 
            $('#theVideo').attr('src','');
        });

        $('.addfavstream').click(function(){
            favid = $(this).data('favstreamid');
            favaction = $(this).data('favaction');
            addtofav(userAnyName, favid, favaction);
            if(favaction == 'add'){
                $(this).data('favaction', 'remove');
                $(this).attr('style','color:red; display:block !important;');
            }else{
                $(this).data('favaction', 'add');
                $(this).attr('style','color:white;');
            }
        });

}


function movieinfotoprowmaterialdataappend(){

    $('.movieInName span').html("");

    movieinfodataappend = "";

    movieinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBody">';
    movieinfodataappend+=' </div>';

         $('#topartget').html(movieinfodataappend);

}

function moviecastinfohtmlappend(data){
    moviecasthtml = "";

    moviecasthtml+='<div class="movieInStarCastScrolls">';
    moviecasthtml+='    <div class="movieInStarCast">';
    moviecasthtml+='        <div class="movieInActNam">';
    moviecasthtml+='            <span>'+ data.name +'</span>'; 
    moviecasthtml+='        </div>';
    moviecasthtml+='        <a class="nav-link text-light" title="'+ data.name +'">';
    if(data.profile_path != null){
        moviecasthtml+='            <img src="https://image.tmdb.org/t/p/original/'+ data.profile_path +'" class="movieperinfo" alt="'+ data.name +'" data-personid="'+ data.id +'">';
    }else{
        moviecasthtml+='            <img src="images/unknown_avatar.png" alt="'+ data.name +'">';
    }
    moviecasthtml+='        </a>';
    moviecasthtml+='    </div>';
    moviecasthtml+='</div>';

    return moviecasthtml;

}

function getpersonInfo(data){
    $('.hiloader').removeClass('hide');
    $('.hiloader').addClass('show');
    personid = data;
    // https://api.themoviedb.org/3/person/35742?api_key=f584f73e8848d9ace559deee1e5a849f

    $.ajax({
        type: "POST",
        url: "includes/requesthandler.php",
        dataType:"text",
        data:{
            action:"callApiForPersonInfoRequest",
            subaction:personid
        }, 
        success: function (data) {
            
            if(data != "")
            {
                moviepersoninfo = JSON.parse(data);
                seriespersoninfoappend(moviepersoninfo);
            }

            $.ajax({
                type: "POST",
                url: "includes/requesthandler.php",
                dataType:"text",
                data:{
                    action:"callApiForPersonImagesRequest",
                    subaction:personid
                }, 
                success: function (response) {
                    if(response != "")
                    {
                        $('.hiloader').removeClass('show');
                        $('.hiloader').addClass('hide');


                        moviespersonimages = JSON.parse(response);                            
                        seriespersonimagesappend(moviespersonimages);
                    }
                }
            });

        }
    });
}


function seriespersoninfoappend(data){



    $('.personame span').html(data['name']);

    seriespersoninfodataappend = "";

    seriespersoninfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBody">';
    seriespersoninfodataappend+='     <div class="row">';
    seriespersoninfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
    seriespersoninfodataappend+='     <div class="movieInPic">';
    seriespersoninfodataappend+='         <img src="https://image.tmdb.org/t/p/original/'+ data['profile_path'] +'" alt="'+ data['name'] +'">';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
    seriespersoninfodataappend+='    <div class="movieInInformation">';
    seriespersoninfodataappend+='         <div class="one"> ';
    seriespersoninfodataappend+='             Date of Birth:';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='        <div class="one">';
    seriespersoninfodataappend+='           Place of Birth:';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='         <div class="one">';
    seriespersoninfodataappend+='             Gender:';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='         <div class="one">';
    seriespersoninfodataappend+='             Known for:';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+=' <div class="col-sm-7 col-xs-7 col-md-7 col-lg-7">';
    seriespersoninfodataappend+='     <div class="movieInInformation">';
    seriespersoninfodataappend+='         <div class="two">';
    if(data['name']){
        seriespersoninfodataappend+='            '+ data['birthday'] +' ';
    }else{
        seriespersoninfodataappend+='            N/A ';
    }
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='         <div class="two">';
    if(data['place_of_birth']){
        seriespersoninfodataappend+='            '+ data['place_of_birth'] +' ';
    }else{
        seriespersoninfodataappend+='            N/A ';
    }
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='         <div class="two">';
    if(data['gender']){
        if(data['gender'] == '1'){
            seriespersoninfodataappend+='            Female';
        }else if(data['gender'] == '2'){
            seriespersoninfodataappend+='            Male';
        }
    }else{
        seriespersoninfodataappend+='            N/A ';
    }
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='         <div class="two">';
    if(data['known_for_department']){
        seriespersoninfodataappend+='            '+ data['known_for_department'] +' ';
    }else{
        seriespersoninfodataappend+='            N/A ';
    }
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+=' <div class="col-sm-1 col-xs-1 col-md-1 col-lg-1">';
    seriespersoninfodataappend+='     <div class="movieInFav">';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    seriespersoninfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    seriespersoninfodataappend+='         <div class="row">';
    seriespersoninfodataappend+='             <div class="movieInDescription">';
    seriespersoninfodataappend+='             '+ data['biography'] +'';
    seriespersoninfodataappend+='             </div>';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';
    seriespersoninfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
    seriespersoninfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInStarCastScroll">';
    seriespersoninfodataappend+='         <div class="row" id="getcastimages" style=" margin: 20px 0px 20px 0px !important; ">';
    seriespersoninfodataappend+='         </div>';
    seriespersoninfodataappend+='     </div>';
    seriespersoninfodataappend+=' </div>';

     $('#topartcastget').html(seriespersoninfodataappend);

     $('.backepisode').click(function(){
        seriesperpersonnulldataappend();
        $('#movieCastInfo').removeClass('show')
        $('#movieCastInfo').addClass('hide')
        $('#movieInfo').removeClass('hide');
        $('#movieInfo').addClass('show');
     });

}


function seriesperpersonnulldataappend(){

    $('.personame span').html("");

    seriesinfodataappend = "";

    seriesinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBody">';
    seriesinfodataappend+=' </div>';

         $('#topartcastget').html(seriesinfodataappend);

}


function seriespersonimagesappend(data){

    seriesperpersonimagesappend = '';

    $.each(data["profiles"], function (index, value) {
        seriesperpersonimagesappend+='           <div class="movieInStarCastScrolls">';
        seriesperpersonimagesappend+='               <div class="movieInStarCast">';
        seriesperpersonimagesappend+='                   <a class="nav-link text-light">';
        if(value['file_path'] != null){
            seriesperpersonimagesappend+='                   <img src="https://image.tmdb.org/t/p/original/'+ value['file_path'] +'">';
        }else{
            seriesperpersonimagesappend+='                   <img src="images/unknown_avatar.png">';
        }
        seriesperpersonimagesappend+='                   </a>';
        seriesperpersonimagesappend+='               </div>';
        seriesperpersonimagesappend+='           </div>';
    });

    $('#getcastimages').html(seriesperpersonimagesappend);
    
}