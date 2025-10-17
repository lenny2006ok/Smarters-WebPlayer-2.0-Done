$(document).ready(function(){
    var currentTime = new Date();
    var getHours = currentTime.getHours();
    var minutesByhour = Number(getHours)*Number(60);
    var extramintsabovehour = currentTime.getMinutes();
    var totalminutes = Number(minutesByhour)+Number(extramintsabovehour);
    var getmargintforcurrent = Number(totalminutes)*Number(8);    
    var forepgmarker = Number(getmargintforcurrent)-Number(200);

    var getDate = currentTime.getDate();
    var getMonth = currentTime.getMonth() < 12 ? currentTime.getMonth() + 1 : 1;
    var getFullYear = currentTime.getFullYear();
    var getMinutes = currentTime.getMinutes();
    var getSeconds = currentTime.getSeconds();
    var dateDatatosent = getDate + '-' + getMonth + '-' + getFullYear + ' ' + getHours + ':' + getMinutes + ":" + getSeconds;


    $("#currentTimeContainer").val(dateDatatosent);
    if(forepgmarker < 200)
        {
            forepgmarker = 0;
    }    
    $('.epg-marker').css('left', getmargintforcurrent+'px');
        
    $('.epg-marker').css('cursor', 'pointer');
    $('.epg-marker').attr('title', getHours+":"+getMinutes);
    $('.epg-marker').data('currentmargin', getmargintforcurrent);
    $('.epg-grid').animate({scrollLeft: '+='+forepgmarker},100);

    getData('<?php echo $categoryid;?>',getmargintforcurrent);

    $('.go-right').click(function() {           
            $('.epg-grid').animate({scrollLeft: '+=300'},100);
            $('#backreload').removeClass('d-none');
    });
    $('.go-left').click(function() {
            $('.epg-grid').animate({scrollLeft: '-=300'},100);
            $('#backreload').removeClass('d-none');

    });

    $('#backreload').click(function(er) {
            er.preventDefault();
             remoadgetmargintforcurrent = $('.epg-marker').data('currentmargin');
            var reloadforepgmarker = Number(remoadgetmargintforcurrent)-Number(200);
            if(reloadforepgmarker < 200)
            {
                reloadforepgmarker = 0;
            } 
            currentscrollposition = $(".epg-grid").scrollLeft();    
            if(currentscrollposition > reloadforepgmarker)
            {
                scroltosec = Number(currentscrollposition)-Number(reloadforepgmarker);
                scroltosec = Number(scroltosec)+Number(200);
                $('.epg-grid').animate({scrollLeft: '-='+scroltosec},100);  
            }
               
            if(currentscrollposition < reloadforepgmarker)
            {
                scroltosec = Number(reloadforepgmarker)-Number(currentscrollposition);
                scroltosec = Number(scroltosec)-Number(200);
                $('.epg-grid').animate({scrollLeft: '+='+scroltosec},100);
            }
            $('#backreload').addClass('d-none');
    });  
});
  
function callSearchFun(){
    $('#noResultFound').remove();
    var SearchData = $("#SearchData").val(); 
    if(SearchData != "")
    {
        $('.channellistcontainer , .commonepgclass ').addClass('d-none');
        var moive_namesearch = $('.serch_key');
        filter = SearchData.toUpperCase();
        CustomIndex = 0;
        moive_namesearch.each(function( index ) {
            if ($( this ).val().toUpperCase().indexOf(filter) > -1) {
                $("."+$(this).data('parentliclass')).removeClass('d-none');
                $("."+$(this).data('epgdataclass')).removeClass('d-none');
                CustomIndex = 1;
                $('.clearSearch').removeClass('d-none');
                $(document).find('.resultSearchLiveEpg').html("");
                $(document).find('.resultSearchLiveEpg').html("Result of <em>"+SearchData+"</em>");
            }
        });  
        if(CustomIndex == 0)
        {
            Swal.fire({
			    position: 'center',
			    type: 'info',
			    html: '<h2 class="text-light">No Match Found !!</h2>',
			    background: 'var(--dark)',
			    showConfirmButton: false,
			    timer: 1500
		    });
            $('.clearSearch').addClass('d-none');
            $('.channellistcontainer , .commonepgclass ').removeClass('d-none');
        }
        $('#search').removeClass('open');      
    }
    else{
        swal('Enter keyword for search.',{button: false});
        setTimeout(function(){swal.close();},2000);
    }     
    $('.loader-submit').addClass('d-none');
    $("#search").modal("hide");
}