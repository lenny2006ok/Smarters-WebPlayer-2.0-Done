$(document).ready(function(){
    $("#movieCategories").addClass('disable');
    $('#getepgstream').addClass('disable');
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
            subaction:"get_live_categories"
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


            // document.getElementById('removecateseach').addEventListener('input', (e) => {
			// 	valuesearch = e.currentTarget.value;
			// 	if(valuesearch == ""){
			// 		cateforyhtmlemend(fulldatalive["category"],"",counts);
			// 	}
			// })


			// $("#removecateseach").click(function(){
			// 	cateforyhtmlemend(fulldatalive["category"],"",counts);
            // });
            
            // document.getElementById('removestreamseach').addEventListener('input', (e) => {
            //     valuesearch = e.currentTarget.value;
            //     if(valuesearch == ""){
            //         setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);
            //         alreatstreamactive = $(".liveFrameBody.active").data("streamid");
            //           nextval = $(".labelclass-"+alreatstreamactive).text();
            //           $(".labelclass-"+alreatstreamactive).html(nextval);
            //     }
            //   })
        
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
                                    if(checkcounter < 101)
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
                                    if(checkcounter < 101)
                                    {
                                        moviestreamhtmlappend+= onlyhtmlembedstreams(value,checkcounter);
                                    }
                                    checkcounter = Number(checkcounter)+Number(1);
                                }
                            });

                                    moviestreamhtmlappend+='<div class="buttons-container centered">';
                                    moviestreamhtmlappend+='<div class="button secondary built-for-cta mobile w-button">Read More...</div>';
                                    moviestreamhtmlappend+='</div>';

                        }
                    }
                    else
                    {
                        moviestreamhtmlappend = "<center class='notfoundepg'> No Streams Found! </center>";
                    }
                        


                    //This function will add counts to cateory
                    addcategorycounters(counts);



                    if(moviestreamhtmlappend != ""){
                        $('#getepgstream').html(moviestreamhtmlappend);
                    }else{
                        $('#getepgstream').html('<center class="notfoundepg"> No Streams Found! </center>');
                    }

                    headername = $('.categoryselect.active').data('toggle');
                    $('.liveHead span').html(headername);

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

    function filterstreamsbyid(categoryid = "all",searchvalue = "")
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
			}
			else
			{
				newStreamshtmltpappend = createhtmlandreturnstrems(categoryid,value,newStreamshtmltpappend);
			}
			
		});


		if(newStreamshtmltpappend != "")
		{
			$("#getepgstream").html(newStreamshtmltpappend);
		}
		else
		{
			$("#getepgstream").html("<center class='notfoundepg'>'"+ searchvalue +"' related result not found! </center>");
			// $("#getepgstream").html('<img class="ldlz" src="images/white-no-data.gif" style="width: 30%;opacity: 1; visibility: visible;">');
		}

            // moviestreamhtmlappend+='<div class="buttons-container centered">';
            // moviestreamhtmlappend+='<div class="button secondary built-for-cta mobile w-button">Read More...</div>';
            // moviestreamhtmlappend+='</div>';


        if(moviestreamhtmlappend != "")
        {
            $("#getepgstream").html(moviestreamhtmlappend);
        }
        // else
        // {
        //     alert('dfsf');
        //     // white-no-data.gif
        //     $("#getepgstream").html('<img class="ldlz" src="images/white-no-data.gif" style="width: 30%;opacity: 1; visibility: visible;">');
        // }
    }   

    function createhtmlandreturnstrems(categoryid = "all",value,newStreamshtmltpappend)
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

                if(checkcounter < 101)
                {
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
                if(checkcounter < 101)
                {
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
                    if(index == 3)
                    {
                        $('#dropdownMenuButton').html(value.category_name);
                        onloadplaycategoryget = "categoryselectonload active";
                    }

                    if(counts != "")
                    {
                        onloadplaycategoryget = "";
                    }

                    movieCatehtmlappend+= onlyhtmlembedcategories(value,onloadplaycategoryget);
            });
                
            $('#dropdownAddData').html(movieCatehtmlappend);
            if(counts != "")
            {
                addcategorycounters(counts);
            }
        }

        $(".categoryselect").click(function(){
            headername = $(this).data('namecate');
            $('#dropdownMenuButton').html(headername);
           /* alert("00001");*/
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


    function addcategorycounters(counts = "")
    {
        if(counts != "")
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
    }

    function showHideLoaderCateData(){
        /*alert("loader on images dilawar");*/
    }



    function onlyhtmlembedstreams(value = "",listnumber = "")
    {
        returndata = "";
        if(value != "")
        {

            returndata +=' <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 epgFrameBody" data-streamid="'+value.stream_id+'">';
            returndata +='     <div class="row">';
            returndata +='         <div class="col-sm-3 col-xs-3 col-md-3 col-lg-3">';
            returndata +='             <div class="epgChanLogo">';
            returndata +='                 <img src="'+value.stream_icon+'" alt="'+value.name+'">';
            returndata +='             </div>';
            returndata +='         </div>';
            returndata +='         <div class="col-sm-9 col-xs-9 col-md-9 col-lg-9">';
            returndata +='             <div class="epgChannelNameList">';
            returndata +='                 <span>'+value.name+'</span>';
            returndata +='             </div>';
            returndata +='         </div>';
            returndata +='     </div>';
            returndata +=' </div>';
        }

        $("#movieCategories").removeClass('disable');
	    $('#getepgstream').removeClass('disable');


        // liveepgfullfunction(value, fullstreamdata, stream );

        return returndata;
    }


    function onlyhtmlembedcategories(value = "",onloadplaycategoryget = "")
    {
        returndata = "";
        if(value != "")
        {
            returndata +=' <a class="dropdown-item categoryselect '+onloadplaycategoryget+'" data-toggle="'+value.category_id+'" data-namecate="'+value.category_name+'" data-cateid="'+value.category_id+'">' +value.category_name+ '</a>';
        }
        return returndata;
    }

    

});
function noposterimage(thisvar = "")
{
    $(".imagesele-" + thisvar).attr("src", "images/NoPoster.png");
}

// function liveepgfullfunction(){
//     $.ajax({
//         type: "POST",
//         url: "includes/requesthandler.php",
//         dataType:"text",
//         data:{
//             action:"callApiRequestFullEPG",
//             subaction:"get_simple_data_table",
//             streamId:value.stream_id
//         }, 
//         success: function (data) {
//             data = JSON.parse(data);

//             $.each(data, function (index, value) {
//                 title =atob(value.title);
//                 // title = value.title;
//             console.log(title);
               
//             });

//         }
//     });
// }