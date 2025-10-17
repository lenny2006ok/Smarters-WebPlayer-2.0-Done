$(document).ready(function(){
    $('#seriesCategories').addClass('disable');
    $('#getseriesstream').addClass('disable');


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
            subaction:"get_series_categories"
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
                    subaction:"get_series"
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
                                    if(checkcounter < 103)
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
                        }
                    }
                    else
                    {
                        moviestreamhtmlappend = "<center class='notfoundepg'> No Streams Found! </center>";
                    }
                        


                    //This function will add counts to cateory
                    addcategorycounters(counts);



                    if(moviestreamhtmlappend != ""){
                        $('#getseriesstream').html(moviestreamhtmlappend);
                    }else{
                        $('#getseriesstream').html('<center class="notfoundepg"> No Streams Found! </center>');
                    }

                    headername = $('.categoryselect.active').data('toggle');
                    $('.liveHead span').html(headername);


                    $('.mainsss').click(function(){
                        $("#topartget").empty();
                        seriesinfotoprowmaterialdataappend();
                        $('#SeriesMain').removeClass('show');
                        $('#SeriesMain').addClass('hide');
                        $('#SeriesInfo').removeClass('hide');
                        $('#SeriesInfo').addClass('show');
                        getname = $(this).data("name");
                        getvodid = $(this).data('vodid');
                        seriesinfogetdata(getvodid,getname);
                    });
                    $('.getmovieInfo').click(function(){
                        $('#SeriesMain').removeClass('hide');
                        $('#SeriesMain').addClass('show');
                        $('#SeriesInfo').removeClass('show');
                        $('#SeriesInfo').addClass('hide');
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

                }
            }); 

        }

    }); 

    function filterstreamsbyid(categoryid = "all",searchvalue = "",dataoffset = 0,datalimit = 101)
    {
        $(".live_body").animate({ scrollTop: 0 }, "fast");
        newStreamshtmltpappend = "";
        checkcounter = 1;
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
                //     // white-no-data.gif
                //     newStreamshtmltpappend = "<center class='notfoundepg'>'"+ searchvalue +"' related result not found! </center>";
                // }
			}
			else
			{
                if(checkcounter >= dataoffset && checkcounter <= datalimit){
				    newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend);
                }
			}
			
        });
        if(newStreamshtmltpappend != "")
		{
            if(checkcounter <= 100){
               $('#getmoviesstream').find('#loadDataappend').hide();
            }else{
                newStreamshtmltpappend+='<div class="buttons-container centered" id="loadDataappend">';
                newStreamshtmltpappend+='<button id="readMstream" data-offset="0" data-limit="100" class="button btn-secondary built-for-cta mobile w-button" style=" font-weight: 500; padding: 6px 20px !important; margin-bottom: 20px !IMPORTANT; border-radius: 6px; ">';
                newStreamshtmltpappend+='Read More... <i id="loadingreader" style="display:none;" class="fa fa-spin fa-spinner" aria-hidden="true"></i>';
                newStreamshtmltpappend+='</button>';
                newStreamshtmltpappend+='</div>';
            }
            
			$("#getseriesstream").html(newStreamshtmltpappend);
		}
		else
		{
			// white-no-data.gif
			$("#getseriesstream").html("<center class='notfoundepg'>Related result not found! </center>");
		}

            // moviestreamhtmlappend+='<div class="buttons-container centered">';
            // moviestreamhtmlappend+='<div class="button secondary built-for-cta mobile w-button">Read More...</div>';
            // moviestreamhtmlappend+='</div>';


        // if(moviestreamhtmlappend != "")
        // {
        //     $("#getseriesstream").html(moviestreamhtmlappend);
        // }
        // else
        // {
        //     // white-no-data.gif
        //     $("#getseriesstream").html('<img class="ldlz" src="images/white-no-data.gif" style="width: 30%;opacity: 1; visibility: visible;">');
        // }
        $('.mainsss').click(function(){
            seriesinfotoprowmaterialdataappend();
            $('#SeriesMain').removeClass('show');
            $('#SeriesMain').addClass('hide');
            $('#SeriesInfo').removeClass('hide');
            $('#SeriesInfo').addClass('show');
            getname = $(this).data("name");
            getvodid = $(this).data('vodid');
            seriesinfogetdata(getvodid,getname);
        });
        $('.getmovieInfo').click(function(){
            $('#SeriesMain').removeClass('hide');
            $('#SeriesMain').addClass('show');
            $('#SeriesInfo').removeClass('show');
            $('#SeriesInfo').addClass('hide');
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
    
    
    // ---------------- For Searching Categories ----------------

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
            $("#seriesCategories").html(searchcategoryhtmlappend);
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
                
            $('#seriesCategories').html(movieCatehtmlappend);
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
            console.log(counts);

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
            if(value.rating.length == 0)
            {
                hideclassrating = "hideratibng";
            }


            classofimage = 'imagesele-' + value.series_id;
            returndata+='<div class="col-sm-2 col-xs-2 col-md-2 col-lg-2 streamselector posterDiv sectabno-'+listnumber+'" data-streamid="'+ value.series_id +'" data-listnumber="'+ listnumber +'">';
            returndata+='    <div class="liveShowImages">';
            returndata+='        <span class="hoverOvers '+hideclassrating+'">'+ value.rating +'</span>';
            returndata+='        <span class="hoverOver" title="'+ value.name +'">'+ value.name +'</span>';
            returndata+='        <a class="nav-link text-light" title="'+ value.name +'">';
            returndata+='            <img class="mainsss '+classofimage+'" src="'+ value.cover +'" alt="'+ value.name +'" data-vodid="'+ value.series_id +'" data-name="'+ value.name +'" onerror="noposterimage(' + value.series_id + ')">';
            returndata+='        </a>';
            returndata+='    </div>';
            returndata+='</div>';
        }
        $("#seriesCategories").removeClass('disable');
	    $('#getseriesstream').removeClass('disable');
        return returndata;
    }


    function onlyhtmlembedcategories(value = "",onloadplaycategoryget = "")
    {
        returndata = "";
        if(value != "")
        {
            returndata += '<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 categoryselect '+onloadplaycategoryget+'" data-toggle="'+value.category_name+'" title="'+value.category_name+'" data-cateid="'+value.category_id+'">';
            returndata += '<div class="row cateList">';
            returndata += '<div class="col-sm-9 col-xs-9 col-md-9 col-lg-9" style="text-align: initial;">';
            returndata += '<div class="liveCateList">';
            returndata += '<span>'+value.category_name+'</span>';
            returndata += '</div>';
            returndata += '</div>';
            returndata += '<div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 cateNum">';
            returndata += '<div class="totalcountare" id="totalstreams-'+value.category_id+'"><img class="ldlz" src="images/golden.svg" style="width: 30%;opacity: 1; visibility: visible;"></div>';
            returndata += '</div>';
            returndata += '</div>';
            returndata += '</div>';
        }
        return returndata;
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

    function seriesinfogetdata(streamId,streamName){

        $('.hiloader').removeClass('hide');
        $('.hiloader').addClass('show');
    
        $.ajax({
            type: "POST",
            url: "includes/requesthandler.php",
            dataType:"text",
            data:{
                action:"callApiRequest",
                subaction:"get_series_info&series_id="+streamId
            }, 
            success: function (data) {
                seasondatashowinbox = [];
    
                if(data != "")
                {
                    $('.hiloader').removeClass('show');
                    $('.hiloader').addClass('hide');
                    seriesfullinfo = JSON.parse(data);
                    
                    // castid = seriesfullinfo['info'].tmdb_id;
                    seriesseasontopdataappend(seriesfullinfo);
                    seriesinfotopdataappend(seriesfullinfo);
                }
    
                    $.ajax({
                        type: "POST",
                        url: "includes/requesthandler.php",
                        dataType:"text",
                        data:{
                            action:"callApiForSeriNameRequest",
                            subaction:streamName
                        }, 
                        success: function (data) {
                            
                            if(data != "")
                            {
                                seriesfullcastinfo = JSON.parse(data);
                                $.each(seriesfullcastinfo['results'], function (index, value) {
                                    originalName = value['original_name'];
                                    if(originalName == streamName){
                                        thisId = value['id'];
                                    } 
                                });
                            }
                
                            $.ajax({
                                type: "POST",
                                url: "includes/requesthandler.php",
                                dataType:"text",
                                data:{
                                    action:"callApiForSeriCastRequest",
                                    subaction:thisId
                                }, 
                                success: function (data) {
                                    
                                    if(data != "")
                                    {
                                        seriesfullcastsinfo = JSON.parse(data);
                                        // castimages = seriesfullcastsinfo['cast']
                                        seriesepisodecast(seriesfullcastsinfo);
                                    }
                                }
                            });
                
                        }
                    });

                    $('.popforseason').click(function(){
                        $('.mainSeriePopSeason').show();
                    });
            } 
        });
    
    }

    // Get Series info Functionality Start

    function seriesinfotopdataappend(data){
        $('.movieInName span').html(data['info'].name);
    
        seriesinfodataappend = "";
    
        seriesinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBody">';
        seriesinfodataappend+='     <div class="row">';
        seriesinfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
        seriesinfodataappend+='     <div class="movieInPic">';
        seriesinfodataappend+='         <img src="'+ data['info'].cover +'" alt="2020"><br>';
        seriesinfodataappend+='         <i class="fa fa-star" aria-hidden="true"></i>';
        seriesinfodataappend+='         <i class="fa fa-star" aria-hidden="true"></i>';
        seriesinfodataappend+='         <i class="fa fa-star-half-o" aria-hidden="true"></i>';
        seriesinfodataappend+='         <i class="fa fa-star" aria-hidden="true" style="color: #4d4a4a;"></i>';
        seriesinfodataappend+='         <i class="fa fa-star" aria-hidden="true" style="color: #4d4a4a;"></i>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+=' <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2">';
        seriesinfodataappend+='    <div class="movieInInformation">';
        seriesinfodataappend+='         <div class="one"> ';
        seriesinfodataappend+='             Directed By:';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='        <div class="one">';
        seriesinfodataappend+='           Release Data:';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="one">';
        seriesinfodataappend+='             Duration:';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="one">';
        seriesinfodataappend+='             Genre:';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="one">';
        seriesinfodataappend+='             Cast:';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+=' <div class="col-sm-7 col-xs-7 col-md-7 col-lg-7">';
        seriesinfodataappend+='     <div class="movieInInformation">';
        seriesinfodataappend+='         <div class="two">';
        if(data['info'].director){
            seriesinfodataappend+='            '+ data['info'].director +' ';
        }else{
            seriesinfodataappend+='            N/A ';
        }
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="two">';
        if(data['info'].releasedate){
            seriesinfodataappend+='            '+ data['info'].releaseDate +' ';
        }else{
            seriesinfodataappend+='            N/A ';
        }
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="two">';
        if(data['info'].duration){
            seriesinfodataappend+='            '+ data['info'].duration +' ';
        }else{
            seriesinfodataappend+='            N/A ';
        }
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="two">';
        if(data['info'].genre){
            seriesinfodataappend+='            '+ data['info'].genre +' ';
        }else{
            seriesinfodataappend+='            N/A ';
        }
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="two">';
        if(data['info'].cast){
            seriesinfodataappend+='             '+ data['info'].cast +'';
        }else{
            seriesinfodataappend+='             N/A';
        }
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='         <div class="three">';
        seriesinfodataappend+='             <button class="playepie">Play - S1:E1</button>';
        seriesinfodataappend+='             <button class="popforseason">Season - 1 <i class="fa fa-chevron-down" aria-hidden="true"></i></button>';
        seriesinfodataappend+='             <button>Watch Trailer</button>';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+=' <div class="col-sm-1 col-xs-1 col-md-1 col-lg-1">';
        seriesinfodataappend+='     <div class="movieInFav">';
        seriesinfodataappend+='         <div class="star">';
        seriesinfodataappend+='             <img src="images/heart.png" alt="2020" onclick="addtofav(this)" data-favsection="series" data-favstreamid="" data-favaction="add">';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
        seriesinfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
        seriesinfodataappend+='         <div class="row">';
        seriesinfodataappend+='             <div class="movieInDescription">';
        seriesinfodataappend+='             '+ data['info'].plot +'';
        seriesinfodataappend+='             </div>';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
        seriesinfodataappend+='     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInStarCastScroll">';
        seriesinfodataappend+='         <div class="row" id="getcastinfo">';
        seriesinfodataappend+='             </div>';
        seriesinfodataappend+='         </div>';
        seriesinfodataappend+='     </div>';
        seriesinfodataappend+=' </div>';
        seriesinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
        seriesinfodataappend+='    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 serieInBtnTab">';
        seriesinfodataappend+='        <div class="row">';
        seriesinfodataappend+='            <div class="serieInBtn">';
        seriesinfodataappend+='                <button class="serieInBtnIn" id="showSerieEpi">Episodes(0)</button>';
        seriesinfodataappend+='                <button id="showSerieCast" style="display:none;">Cast</button>';
        seriesinfodataappend+='            </div>';
        seriesinfodataappend+='            <hr>';
        seriesinfodataappend+='        </div>';
        seriesinfodataappend+='    </div>';
        seriesinfodataappend+='    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" id="seriesEpiList" style="padding-top: 20px !important;">';
        seriesinfodataappend+='    </div>';
        seriesinfodataappend+='    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" id="seriesEpiCast" style="display: none;">';
        seriesinfodataappend+='        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInStarCastScroll">';
        seriesinfodataappend+='            <div class="row" id="seriesepicast">';
        seriesinfodataappend+='            </div>';
        seriesinfodataappend+='        </div>';
        seriesinfodataappend+='    </div>';
        seriesinfodataappend+='</div>';

        $('#topartget').html(seriesinfodataappend);
        var imagefornoepima = data['info'].cover
        seriesepisodehtml(data,1,imagefornoepima);
        
        $('#showSerieCast').click(function(){
            $('#seriesEpiList').hide();
            $('#seriesEpiCast').show();
            $('#showSerieEpi').removeClass('serieInBtnIn');
            $('#showSerieCast').addClass('serieInBtnIn');
        });
        $('#showSerieEpi').click(function(){
            $('#seriesEpiCast').hide();
            $('#seriesEpiList').show();
            $('#showSerieCast').removeClass('serieInBtnIn');
            $('#showSerieEpi').addClass('serieInBtnIn');
        });
    }


    // Get Seasons Functionality Start

    function seriesseasontopdataappend(data){
        seriesseasonsdataappend = '';
        seriesseasonsdataappend+='<div class="row">';
        seriesseasonsdataappend+='    <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2"></div>';
        seriesseasonsdataappend+='    <div class="col-sm-8 col-xs-8 col-md-8 col-lg-8 centerSeriePopSeason">';
        seriesseasonsdataappend+='        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">';
        seriesseasonsdataappend+='            <div class="serieSeasPopHeader">';
        seriesseasonsdataappend+='                <span>Seasons</span>';
        seriesseasonsdataappend+='            </div>';
        seriesseasonsdataappend+='            <div class="serieSeasPopBody">';
        // seriesseasonsdataappend+='                <button style="color: #ffff; background-color: #00f3ff; background-image: linear-gradient(to right, #2586ff , #00ffad);">Season - 1</button>';
        var y = 0;
        for (i = 0; i < data['seasons'].length; i++) {
            y++;
                activetor = "";
                if(y == 1)
                {
                    activetor = "sessonBTN";
                }
            // countepisder = ;
            if (data['episodes'][y] != undefined) {
                seriesseasonsdataappend+='                <button class="changeseason '+ activetor +'" data-seasonnum="'+ y +'" data-epicounter="">Season '+ y +'</button>';
            }
        }
        seriesseasonsdataappend+='            </div>';
        seriesseasonsdataappend+='            <div class="serieSeasPopFooter">';
        seriesseasonsdataappend+='                <button class="closePopSea">Close</button>';
        seriesseasonsdataappend+='            </div>';
        seriesseasonsdataappend+='        </div>';
        seriesseasonsdataappend+='    </div>';
        seriesseasonsdataappend+='    <div class="col-sm-2 col-xs-2 col-md-2 col-lg-2"></div>';
        seriesseasonsdataappend+='</div>';
        
        $('#serieSession').html(seriesseasonsdataappend);

        $('.closePopSea').click(function(){
            $('.mainSeriePopSeason').hide();
        });

        $('.changeseason').click(function(){
            $('.changeseason').removeClass('sessonBTN');
            $(this).addClass('sessonBTN');
           getseasonid = $(this).data('seasonnum');
           getepicount = $(this).data('epicounter');
           seriesepisodehtml(data,getseasonid,'');
        //    $('#showSerieEpi').html('Episodes ('+ getepicount +')');
           $('.popforseason').html('Season - '+ getseasonid +' <i class="fa fa-chevron-down" aria-hidden="true"></i>');
           $('.playepie').html('Play - S'+getseasonid+':E1');
           $('.mainSeriePopSeason').hide();
        });
    }

    function seriesepisodecast(data){
        fulldata = data['cast'];
        seriescastdataappend = '';

        // seriescastdataappend =' <center style="font-size: 28px; font-weight: 600; margin-top: 5% !important;"> Series Cast Underworking </center>';    
        $.each(fulldata, function (index, value) {
            castimages = value['profile_path'];
            castname = value['name'];
            personId = value['id'];

            if(castimages){
                $('#showSerieCast').show();
            }

            seriescastdataappend+='                <div class="movieInStarCast">';
            seriescastdataappend+='                    <div class="movieInActNam">';
            seriescastdataappend+='                        <span>'+ castname +'</span>';
            seriescastdataappend+='                    </div>';
            seriescastdataappend+='                    <a class="nav-link text-light" title="'+ castname +'">';
            if(castimages != null){
                seriescastdataappend+='                        <img src="https://image.tmdb.org/t/p/original'+castimages+'" class="personinfo" alt="'+castimages+'" data-personid="'+ personId +'">';
            }else{
                seriescastdataappend+='                        <img src="images/unknown_avatar.png" class="personinfo" alt="'+castimages+'" data-personid="'+ personId +'">';
            }
            seriescastdataappend+='                    </a>';
            seriescastdataappend+='                </div>';

        });
        $('#seriesepicast').append(seriescastdataappend);


        $('.personinfo').click(function(){
            // $("#topartget").empty(); 
            $('#SeriesInfo').removeClass('show');
            $('#SeriesInfo').addClass('hide');
            $('#seriePersonCastInfo').removeClass('hide');
            $('#seriePersonCastInfo').addClass('show');
            onepersonId = $(this).data('personid');
            getpersonInfo(onepersonId);
        });

    }

    function seriesepisodehtml(data,epinum,mainima){

        seriesepisodedataappend = '';

        epicount = 0;
        $.each(data['episodes'][epinum], function (index, value) {
            epicount++;
            $('#showSerieEpi').text('Episodes('+epicount+')');

            // serieInEpisode class
            seriesepisodedataappend+='        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 serieInEpisode" style="height: auto;">';
            seriesepisodedataappend+='            <div class="row">';
            if(value['info'].movie_image != undefined){
                seriesepisodedataappend+='                <div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 col-xs-3 col-md-3 col-lg-3"  style=" background-image: url(images/play.png),url('+ value['info'].movie_image +');background-position: center,center; background-size: auto,cover; background-repeat: no-repeat; margin-top: 8px !important; border-radius: 7px;">';
            }else{
                seriesepisodedataappend+='                <div class="col-sm-3 col-xs-3 col-md-3 col-lg-3 col-xs-3 col-md-3 col-lg-3"  style=" background-image: url(images/play.png),url('+ mainima +');background-position: center,center; background-size: auto,cover; background-repeat: no-repeat; margin-top: 8px !important; border-radius: 7px;">';
            }
            seriesepisodedataappend+='                </div>';
            seriesepisodedataappend+='                <div class="col-sm-9 col-xs-9 col-md-9 col-lg-9">';
            seriesepisodedataappend+='                    <div class="serieEpiName">';
            seriesepisodedataappend+='                        <span>'+ value.title +'</span>';
            seriesepisodedataappend+='                    </div>';
            seriesepisodedataappend+='                    <div class="serieEpiRate">';
            seriesepisodedataappend+='                        <span><i class="fa fa-star" aria-hidden="true"></i>';
            seriesepisodedataappend+='                            <i class="fa fa-star" aria-hidden="true"></i>';
            seriesepisodedataappend+='                            <i class="fa fa-star" aria-hidden="true"></i>';
            seriesepisodedataappend+='                            <i class="fa fa-star" aria-hidden="true"></i>';
            seriesepisodedataappend+='                            <i class="fa fa-star-half-o" aria-hidden="true"></i>';
            seriesepisodedataappend+='                        </span>';
            seriesepisodedataappend+='                    </div>';
            seriesepisodedataappend+='                    <div class="serieEpiDura">';
            seriesepisodedataappend+='                        <span>';
            if(value['info'].duration_secs){
                var m = Math.floor(value['info'].duration_secs % 3600 / 60);
                seriesepisodedataappend+='                            <label>'+ m +'m</label>';
            }else{
                seriesepisodedataappend+='                            <label>N/A</label>';
            }
            seriesepisodedataappend+='                        </span>';
            seriesepisodedataappend+='                    </div>';
            seriesepisodedataappend+='                    <div class="serieEpiDesc">';
            if(value['info'].plot){
                seriesepisodedataappend+='                        <span>'+ value['info'].plot +'</span>';
            }else{
                seriesepisodedataappend+='                        <span>N/A</span>';
            }
            seriesepisodedataappend+='                    </div>';
            seriesepisodedataappend+='                </div>';
            seriesepisodedataappend+='            </div>';
            seriesepisodedataappend+='        </div>';
        });

        $('#seriesEpiList').html(seriesepisodedataappend);
    }

    function seriesinfotoprowmaterialdataappend(){

        $('.movieInName span').html("");
    
        seriesinfodataappend = "";
    
        seriesinfodataappend+='<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 movieInBodyScroll" >';
        seriesinfodataappend+=' </div>';
    
             $('#topartget').html(seriesinfodataappend);
    
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
                    seriespersoninfo = JSON.parse(data);
                    seriespersoninfoappend(seriespersoninfo);
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


                            seriespersonimages = JSON.parse(response);                            
                            seriespersonimagesappend(seriespersonimages);
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
            $('#seriePersonCastInfo').removeClass('show')
            $('#seriePersonCastInfo').addClass('hide')
            $('#SeriesInfo').removeClass('hide');
            $('#SeriesInfo').addClass('show');
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
            // seriesperpersonimagesappend+='                   <div class="movieInActNam">';
            // seriesperpersonimagesappend+='                       <span>'+ data['name'] +'</span>'; 
            // seriesperpersonimagesappend+='                   </div>';
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
    

});
function noposterimage(thisvar = "")
{
    $(".imagesele-" + thisvar).attr("src", "images/NoPoster.png");
}

