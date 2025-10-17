$(document).ready(function () {
    var currentthemename = $('#currentthemename').val();
    var userAnyName = $('#usernames').val();
    var selectedtime = '12';
    var getimeformat = getthesavedtimeformat();
    if(getimeformat){
        if(getimeformat != null){
            if(getimeformat != ''){
                selectedtime = getimeformat[0]['timeFormat'];
            }
        }        
    }
    getDateWithBothFormats(selectedtime);
    
    $('#showSerieCast').click(function(){
        // alert();
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
    $('.topsetshowpop').click(function(){
        $('.topsetshowpop').removeClass('shows');
        $('.topsetshowpop').addClass('hide');
        $('.topsethidepop').removeClass('hide');
        $('.topsethidepop').addClass('shows');
        $('.header_popup').removeClass('hide');
        $('.header_popup').addClass('show');
    });
    $('.topsethidepop').click(function(){
        $('.topsethidepop').removeClass('shows');
        $('.topsethidepop').addClass('hide');
        $('.topsetshowpop').removeClass('hide');
        $('.topsetshowpop').addClass('shows');
        $('.header_popup').removeClass('show');
        $('.header_popup').addClass('hide');
    });
    $('.closepopsortbtn').click(function(){
        $('.sortpop').removeClass('show');
        $('.sortpop').addClass('hide');
    });

	$(document).on("click",'#homeshort,#movieshort,#liveshort,#serieshort,#settingshort,#sortshort',function(){
    
        var id = $(this).attr("id");

        if(id == 'homeshort'){
            window.location.href = 'dashboard.php';
        }else if(id == 'movieshort'){
            window.location.href = 'movies.php';
        }else if(id == 'liveshort'){
            window.location.href = 'live.php';
        }else if(id == 'serieshort'){
            window.location.href = 'series.php';
        }else if(id == 'settingshort'){
            window.location.href = 'settings.php';
        }else if(id == 'sortshort'){
            $('.topsethidepop').removeClass('shows');
            $('.topsethidepop').addClass('hide');
            $('.topsetshowpop').removeClass('hide');
            $('.topsetshowpop').addClass('shows');
            $('.header_popup').removeClass('show');
            $('.header_popup').addClass('hide');
            $('.sortpop').removeClass('hide');
            $('.sortpop').addClass('show');
        }
    });

    $('#scrollCastDivLeft').click(function(){
        $('.movieInStarCastScrolls').animate({scrollLeft: '+=300'},500);
        $('#scrollCastDivRight').removeClass('d-none');
    });
    $('#scrollCastDivRight').click(function(){
        $('.movieInStarCastScrolls').animate({scrollLeft: '-=300'},500);
    });
    $('.openPopSea').click(function(){
        $('.mainSeriePopSeason').show();
    });
    $('.closePopSea').click(function(){
        $('.mainSeriePopSeason').hide();
    });
    $('#scrollCastDivLeft').click(function(){
        $('.epgRightFullScroll').animate({scrollLeft: '-=300'},500);
        $('.epgRightBottomScroll').animate({scrollLeft: '-=300'},500);
    });
    $('#scrollCastDivRight').click(function(){
        $('#scrollCastDivLeft').show();
        $('.epgRightFullScroll').animate({scrollLeft: '+=300'},500);
        $('.epgRightBottomScroll').animate({scrollLeft: '+=300'},500);
        $('#scrollCastDivRight').removeClass('d-none');
    });
    $('#closePopUP').on('click',function(){
        $(".live_body").attr("style","width:100% !important; left:0px;");
        $('.rightCategoryDiv').hide('1000', function(){
            $(".fullLiveDiv").attr("class","col-sm-12 col-xs-12 col-md-12 col-lg-12 fullLiveDiv padding");
        });
        $(".live_header").attr("style","width:100%;");
        $('.fullLiveleftheader').show();
        $('.freeDiv').attr("class","col-sm-10 col-xs-10 col-md-10 col-lg-10 freeDiv padding");
        $(".fullLiverightheader").attr("class","col-sm-1 col-xs-1 col-md-1 col-lg-1 fullLiverightheader padding");
        $(".centerSearch").addClass('forcenterSearch');
        $(".posterDiv").attr("class","col-sm-2 col-xs-2 col-md-2 col-lg-2 posterDiv padding");
        $(".posterDiv").attr("style","width:12.5% !important;");
    });
    $('#showPopUP').click(function(){
        $('.rightCategoryDiv').show({direction: 'right'});
        $(".fullLiveDiv").attr("class","col-sm-9 col-xs-9 col-md-9 col-lg-9 fullLiveDiv padding");
        $('.fullLiveleftheader').hide();
        $(".live_header").removeAttr("style");
        $(".live_body").removeAttr("style");
        $(".centerSearch").removeClass('forcenterSearch');
        $(".centerSearch").hide();
        $('.freeDiv').attr("class","col-sm-11 col-xs-11 col-md-11 col-lg-11 freeDiv padding");
        $(".fullLiverightheader").attr("class","col-sm-1 col-xs-1 col-md-1 col-lg-1 fullLiverightheader padding");
        $(".posterDiv").attr("class","col-sm-2 col-xs-2 col-md-2 col-lg-2 posterDiv padding");
        $(".posterDiv").removeAttr("style");
    });
    $('.searchBar').click(function(){
        $('.liveInsearch').hide();
        $('.liveHead span').hide();
        $('.searchBar').attr('style','opacity:0;');
        $('.liveback').hide();
        $('.div6').hide();
        $('.div1').attr("class","col-sm-7 col-xs-7 col-md-7 col-lg-7 div1 padding");
        $('.centerSearch').show({direction: 'left'});
        $('.livecenterSearch').show({direction: 'left'});
        $('.SearchStreams').select();
        $('#removestreamseach').attr('style','caret-color: white;');  

    });
    $('.closeSearch').click(function(){
        $('.centerSearch').hide();
        $('.livecenterSearch').hide();
        $('.liveHead span').show();
        $('.searchBar').removeAttr("style");
        $('.liveInsearch').show();
        $('.liveback').show();
        $('.div6').show();
        $('.div1').attr("class","col-sm-1 col-xs-1 col-md-1 col-lg-1 div1 padding");
        $('.SearchStreams').val('');
    })

    // live search start
    // $('.searchBar').click(function(){
    //     $('.livesearchBar').show();
    //     $('.div5').hide();
    //     $('.div1').attr("class","col-sm-6 col-xs-6 col-md-6 col-lg-6 div6");
    //     $(this).hide();
    //     $('.centerSearch').show({direction: 'left'});
    // });
    // $('.livecloseSearch').click(function(){
    //     $(this).hide();
    //     $('.div5').show();
    //     $('.div6').attr("class","col-sm-1 col-xs-1 col-md-1 col-lg-1 div1");
    //     $('.livesearchBar').show();
    // })

    // live search end

    $('.setGernelSetting').hover(function(){
        $('.setGernelSetting img').attr('src', 'themes/'+currentthemename+'/images/general_srttings_focused.png');
        $('.setEpg img').attr('src', 'themes/'+currentthemename+'/images/epg.png');
        $('.setTimeFormat img').attr('src', 'themes/'+currentthemename+'/images/time_format.png');
        $('.setParentalCont img').attr('src', 'themes/'+currentthemename+'/images/parental_controll.png');
        $('.setPlayerSel img').attr('src', 'themes/'+currentthemename+'/images/player_section.png');
    });
    $('.setEpg').hover(function(){
        $('.setEpg img').attr('src', 'themes/'+currentthemename+'/images/epg_focused.png');
        $('.setGernelSetting img').attr('src', 'themes/'+currentthemename+'/images/general_srttings.png');
        $('.setTimeFormat img').attr('src', 'themes/'+currentthemename+'/images/time_format.png');
        $('.setParentalCont img').attr('src', 'themes/'+currentthemename+'/images/parental_controll.png');
        $('.setPlayerSel img').attr('src', 'themes/'+currentthemename+'/images/player_section.png');
    });
    $('.setTimeFormat').hover(function(){
        $('.setTimeFormat img').attr('src', 'themes/'+currentthemename+'/images/time_format_focused.png');
        $('.setGernelSetting img').attr('src', 'themes/'+currentthemename+'/images/general_srttings.png');
        $('.setEpg img').attr('src', 'themes/'+currentthemename+'/images/epg.png');
        $('.setParentalCont img').attr('src', 'themes/'+currentthemename+'/images/parental_controll.png');
        $('.setPlayerSel img').attr('src', 'themes/'+currentthemename+'/images/player_section.png');
    });
    $('.setParentalCont').hover(function(){
        $('.setParentalCont img').attr('src', 'themes/'+currentthemename+'/images/parental_controll_focused.png');
        $('.setGernelSetting img').attr('src', 'themes/'+currentthemename+'/images/general_srttings.png');
        $('.setTimeFormat img').attr('src', 'themes/'+currentthemename+'/images/time_format.png');
        $('.setEpg img').attr('src', 'themes/'+currentthemename+'/images/epg.png');
        $('.setPlayerSel img').attr('src', 'themes/'+currentthemename+'/images/player_section.png');
    });
    $('.setPlayerSel').hover(function(){
        $('.setPlayerSel img').attr('src', 'themes/'+currentthemename+'/images/player_section_focus.png');
        $('.setParentalCont img').attr('src', 'themes/'+currentthemename+'/images/parental_controll.png');
        $('.setGernelSetting img').attr('src', 'themes/'+currentthemename+'/images/general_srttings.png');
        $('.setTimeFormat img').attr('src', 'themes/'+currentthemename+'/images/time_format.png');
        $('.setEpg img').attr('src', 'themes/'+currentthemename+'/images/epg.png');
    });

    $('.inputBox').keypress(function () {
        $(this).removeClass("aborder");
    });

    $('.inputBox').click(function(){
        $(this).attr('style','caret-color: white;'); 
    });
    $('#removecateseach').click(function(){
        $(this).attr('style','caret-color: white;');  
    });

    (function() {
        var target1 = $(".epgFrameBodyScroll");
        var target2 = $(".epgRightFullScroll");
        var target3 = $(".epgRightBottomScroll");
        $(target3).scroll(function() {
            target1.prop("scrollTop", this.scrollTop)
            target2.prop("scrollLeft", this.scrollLeft);
        });
        $(target1).scroll(function() {
            target3.prop("scrollTop", this.scrollTop);
        });
        $(target2).scroll(function() {
            target3.prop("scrollLeft", this.scrollLeft)
            target2.prop("scrollLeft", this.scrollLeft);
        });
    })();

    // (function() {
    //     var target1 = $(".epgFrameBodyScroll");
    //     var target2 = $(".epgRightFullScroll");
    //     var target3 = $(".epgRightBottomScroll");
    //     $(target3).scroll(function() {
    //         target1.prop("scrollTop", this.scrollTop)
    //         target2.prop("scrollLeft", this.scrollLeft);
    //     });

    //     $(target1).scroll(function() {
    //         target3.prop("scrollTop", this.scrollTop)
    //         target2.prop("scrollLeft", this.scrollLeft);
    //     });
    // })();

    // setInterval(function(){
    //     var date = new Date();
    //     var hours = date.getHours();
    //     var minutes = date.getMinutes();
    //     var ampm = hours >= 12 ? 'PM' : 'AM';
    //     hours = hours % 12;
    //     hours = hours ? hours : 12; // the hour '0' should be '12'
    //     minutes = minutes < 10 ? '0'+minutes : minutes;
    //     var strTime = hours + ':' + minutes + ' ' + ampm;
    //     $('.time').html(strTime);
    // },1000)

    function getDateWithBothFormats(type){
        var data = new Date();
        var hours = data.getHours();
        var minutes = data.getMinutes();
   
        if(type == "24"){
             if(hours <= 9)
                  hours =  '0' + hours ; 
             if(minutes <= 9)
                  minutes =  '0' + minutes ; 
   
             var date = hours + ":"+minutes;
   
        }else{
             var ampm = hours >= 12 ? 'PM' : 'AM';
             hours = hours % 12;
             hours = hours ? hours : 12; // the hour '0' should be '12'
             minutes = minutes < 10 ? '0'+minutes : minutes;
             var date = hours + ':' + minutes + ' ' + ampm; 
        }
        $('.time').html(date);

        // return date;
   }
   function getthesavedtimeformat(){
        storedtimevalue = "";
        addedtimevalue = localStorage.getItem("timeformatsetting");
        newaddedtimevalue = JSON.parse(addedtimevalue);
         if(newaddedtimevalue != null)
        {
            if(typeof newaddedtimevalue[userAnyName] != "undefined") 
            {
                storedtimevalue = newaddedtimevalue[userAnyName];
            }
        }
        return storedtimevalue;
    }

    $('.underWorking').click(function(){
            alert("Under Workig");
            window.location.href = 'dashboard.php';
    });


    $('.mobiClickfull').click(function(){
        devicerotationdetect = '';
        if (window.matchMedia("(orientation: portrait)").matches) {
            devicerotationdetect = 'landscape';
         }

        if (window.matchMedia("(orientation: landscape)").matches) {
            devicerotationdetect = 'andscape-primary';
        }
        devicerotation(devicerotationdetect);
        
    });
         
    $('#mobiClick').click(function(){
       lock('landscape-primary');
    });

    function devicerotation(detected){
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
            document.documentElement.msRequestFullscreen();
        }
      
        screen.orientation.lock(detected);
    }


    //  if (window.matchMedia("(orientation: portrait)").matches) {
    //         // $('.mobiClickfull').toggle(); 
    //         alert('portrait');
    //  }
     
    //  if (window.matchMedia("(orientation: landscape)").matches) {
    //     // $('.mobiClickfull').toggle(); 
    //     alert('landscape');
    // }
    
  

    // lock('landscape-primary');
    // $(this).removeClass('mobiClickfull');
    // $(this).addClass('mobiClick');

});
   