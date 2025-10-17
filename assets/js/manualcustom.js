$(document).ready(function() {
  var closed = localStorage.getItem('closed') || '';
  if(closed == 'yes')
  {
    $('.warningPerm').hide(0);
  }
  else
  {
    $('.warningPerm').removeClass('hide');
  }
$('#LoginModal').click(function(){
  $('#loginModel').modal({
  backdrop: true,
  keyboard: true
})
})


var webtvloginprocess = $('.webtvloginprocess');
webtvloginprocess.click(function(e){
    e.preventDefault();
    formsubmitwebtv();
});


function formsubmitwebtv(runtime = 0)
{
   

  $('#loginProcessIcon').addClass('d-none');
    var LoginImageLoader = $('#LoginImageLoader');
    var totalinput = 0;
    $('.logininputs').removeClass('addborder');
    LoginImageLoader.removeClass('d-none');
    $(this).addClass('anchordeactivate');
    var username_identy = $('#input-login');
    var password_identy = $('#input-pass');
    var username_value = $('#input-login').val();
    var password_value = $('#input-pass').val();
    var rememberMe_value = '';
    if($('#rememberMe').is(':checked'))
    {
      rememberMe_value = 'on';
      
    }
    else
    {
      rememberMe_value = 'off'
    }
  
    if(username_value != "")
    {
        totalinput++;
    }
    else
    {
        username_identy.addClass('addborder');
    }
    if(password_value != "")
    {
        totalinput++;
    }
    else
    {
        password_identy.addClass('addborder');
    }
    if(totalinput == 2)
    {
      $('#loginProcessIcon').removeClass('d-none');
      jQuery.ajax({
          type:"POST",
          url:"includes/ajax-control.php",
          dataType:"text",
          data:{
          action:'webtvlogin',
          uname:username_value,
          upass:password_value,
          runtime:runtime,
          rememberMe:rememberMe_value
          },  
          success:function(response){

             $('#loginProcessIcon').addClass('d-none');
              LoginImageLoader.addClass('d-none');  
              var obj = jQuery.parseJSON(response);
              console.log(obj);
              if(obj.result != "error")
              {
                window.location.href = 'dashboard.php';
              }
              else
              {
                console.log(runtime);
                if(runtime < 2)
                {
                  if(obj.message != "Status is Expired" && obj.message != "Your account is blocked for WebTv Player - Please contact the owner!")
                  {
                    runtime = Number(runtime)+Number(1)
                    formsubmitwebtv(runtime);
                  }
                  else
                  {
                     $('.loginprocess').removeClass('anchordeactivate');
                     swal('Error!',obj.message,'warning');
                  }
                }
                else
                {
                  if(obj.message == "" || obj.message == null)
                  {
                    obj.message = "Invalid details";
                  }
                  $('.loginprocess').removeClass('anchordeactivate');
                  swal('Error!',obj.message,'warning');
                }
              }  
          
          }
        }); 
    }
    else
    {
      LoginImageLoader.addClass('d-none');
      $('.loginprocess').removeClass('anchordeactivate');
    }
}

var logoutBtn = $('.logoutBtn');
    logoutBtn.click(function(e){
      e.preventDefault();
        const swalWithBootstrapButtons = Swal.mixin({
          confirmButtonClass: 'btn btn-success m-3',
          cancelButtonClass: 'btn btn-danger m-3',
          buttonsStyling: false,
        })
        
        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, logout me!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
              jQuery.ajax({
                  type:"POST",
                  url:"includes/ajax-control.php",
                  dataType:"text",
                  data:{
                  action:'logoutProcess'
                  },  
                  success:function(response){
                    if(response == "1")
                    {
                      localStorage.setItem("clientlogoutmessage", "yes");
                      window.location.href = 'index.php';
                    }
                  }
              });
          } 
        })
     /* Swal.fire({
        title: 'Are you sure to logout?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Logout!'
      }).then((result) => {
        if (result.value) {

            jQuery.ajax({
                type:"POST",
                url:"includes/ajax-control.php",
                dataType:"text",
                data:{
                action:'logoutProcess'
                },  
                success:function(response){  
                  localStorage.removeItem('isshow');
                   localStorage.removeItem('closed');
                  window.location.href = 'index.php?loggedout';
                }
              });
        }
      });*/
      
    
    });  

$('.dontShow').click(function(){
  
  
    if (closed != 'yes') {
     localStorage.setItem('closed','yes');
    }
    
})



  


/*
	var PlayerDIvSelector = $('#player-wrapper');
	var PlayerDivLenth = PlayerDIvSelector.length;
	if(PlayerDivLenth != 0){
		PlayerDIvSelector.html('');
		jwplayer("player-wrapper").setup({
		    "file": "http://qqtv.nl:80/movie/lovedeep/lovedeep/18628.mp4",
		    "width": "100%",
		    "aspectratio": "16:9"
		});
	}*/
});