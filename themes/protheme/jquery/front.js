$(document).ready(function(){
    currentthemename = $('#currentthemename').val();
    $(".showpass").click(function(){
        current = $(this).data("current");
        if(current == "hide")
        {
            $(this).data("current","show");            
            $("#input-pass").attr("type","texts");
            // $("#input-pass").attr("type","text");
            $(".hideeye").hide();
            $(".showeye").show();
        }
        else
        {
            $(this).data("current","hide");   
            $("#input-pass").attr("style");
            $("#input-pass").attr("type","password");
            $(".showeye").hide();
            $(".hideeye").show();
        }
    });

    $(document).keypress(function(e) {
        if(e.which == '13'){
           $('#add_user').click();
        }
    }); 

    
    $('.inputBox').focusout(function(){
        $('.inputBox').parent('.input-group').removeClass('showborder');
    })
    $('.inputBox').focus(function(){
        $('.inputBox').parent('.input-group').removeClass('showborder');
        $(this).parent('.input-group').addClass('showborder');
    })

    $('#add_user').click(function(){  
        
        validateflds = validateloginfields();
        if(validateflds == "1")
        {
            var antName = $('#input-anyName').val();  
            var Uname = $('#input-login').val();
            var Pname = $('#input-pass').val();  
            var remember = $('#rememberMe').is(':checked');  
            var action = 'webtvlogin';

            localData = localStorage.getItem("listUser");

            duplicateAnynameValidation = "0";
            if(localData != null && localData != "")
            {
                decoded = JSON.parse(localData);
                jQuery.each(decoded, function(index, item) {
                   jQuery.each(item, function(index2, item2) {
                       if(index2 == antName)
                       {
                            duplicateAnynameValidation = "1";
                       }
                    });
                });
            }
            if(duplicateAnynameValidation == "0")
            {

                portalkey = "";
                if($("#input-portal").length > 0)
                {
                    portalkey = $("#input-portal").val();
                }


                $('.checkingspin').removeClass('hide');
                $('#add_user').attr('disabled','disabled');
                currentthemename = $('#currentthemename').val();  
                $.ajax({
                    type: "POST",
                    url: "themes/"+currentthemename+"/includes/requesthandler.php",
                    dataType:"text",
                    data:{
                        anyname:antName,
                        username:Uname,
                        password:Pname,
                        action:action,
                        portalkey:portalkey,
                        rememberme:remember
                    }, 
                    success: function (data) {
                        if(typeof data == "string"){
                            data = JSON.parse(data);
                        }
                        if(data.result == 'success'){

                            addTolistUser(data.detailsarr);    
                            window.location.href = 'dashboard.php';
                        }else{
                            showMsg("Invaild Username/Password","error");
                        }
                    },
                    complete: function () {
                        $('.checkingspin').addClass('hide');
                        $('#add_user').removeAttr('disabled');
                    }
                });  
            }
            else
            {
                showMsg("Name: ("+antName+") already in use" ,"error");
            }
        }
        else
        {
            showMsg("Please enter required fields","error");
        }
    });





    $('.log_out').click(function(){
        var action = 'logoutProcess';
        currentthemename = $('#currentthemename').val();  
        $.ajax({
            type: "POST",
            url: "themes/"+currentthemename+"/includes/requesthandler.php",
            dataType:"text",
            data:{
                action:action
            }, 
            success: function (data) {      
                window.location.href = 'index.php';
            },
            complete: function () {
                window.location.href = 'index.php';
            }
        });
    });


// Switch User functionality start from here

    var Textb = "";
    var vaitem = {};
    localData = localStorage.getItem("listUser");
    if(localData != null && localData != "")
    {
        decoded = JSON.parse(localData);
        customindexing = 1;
        jQuery.each(decoded, function(index, item) {
           jQuery.each(item, function(anyname, dataarr) {
             vaitem[anyname] = dataarr;
            Textb+= '';
                Textb+='<div class="col-sm-4 getUserList common-'+customindexing+'" data-anynameis='+anyname+'>'
                Textb+='    <div class="col-sm-12">'
                Textb+='        <div class="row listBanner">'
                Textb+='            <div class="col-sm-3 padding">'
                Textb+='                <div class="userlist">'
                Textb+='                    <span>'
                Textb+='                        <img src="themes/'+currentthemename+'/images/user-icon.png" alt="user List">'
                Textb+='                    </span>'
                Textb+='                </div>'
                Textb+='            </div>'
                Textb+='            <div class="col-sm-9 padding userlistcardleft">'
                Textb+='                <span class="anyName">'+ anyname +'</span><br>'
                Textb+='                <span class="userlistshowname">Username: '+ dataarr.username +'</span>'
                Textb+='            </div>'
                Textb+='            <div class="col-sm-12">'
                Textb+='                <div class="dropdown-content" id="ListUserModal" tabindex="-1" data-parentsec=""> '
                Textb+='                    <div class="login_link_0 getList loginfromlist" data-customindexing='+customindexing+'>LOGIN</div> '
                Textb+='                    <div class="delete_link_0 deletelist" data-customindexing='+customindexing+'>DELETE</div>'
                Textb+='                </div> '
                Textb+='            </div>'
                Textb+='        </div>'
                Textb+='    </div> '
                Textb+='</div>';
                customindexing = Number(customindexing)+Number(1);
            });
        });
    }
    appendata = '<center style="margin-top: 20px !important;"><a href="index.php" style=" text-decoration: none; color: white;"><span> <img src="themes/'+currentthemename+'/images/add_more_user.png" style=" width: 4%; backdrop-filter: opacity(0.5);"><br><label style="cursor: pointer;">ADD NEW USER</label></span></a></center>';
    if(Textb != "")
    {
        appendata = Textb;
    }
    $('.listUserDIV').html(appendata);

    $(".loginfromlist").click(function(){
        custdxng = $(this).data("customindexing");
        mainselector = $(".common-"+custdxng);
        $(".getUserList").addClass('not-active');
        mainselector.find('.listBanner').addClass('card-loader card-loader--tabs');
        mainselector.find('.listBanner').attr('style','background: linear-gradient(to right, #f08544, #da3657); color: white;');
        anynameis = mainselector.data("anynameis");
        currentthemename = $('#currentthemename').val();  
        $.ajax({
            type: "POST",
            url: "themes/"+currentthemename+"/includes/requesthandler.php",
            dataType:"text",
            data:{
                anyname:anynameis,
                username:vaitem[anynameis]["username"],
                password:vaitem[anynameis]["password"],
                portallink:vaitem[anynameis]["portallink"],
                action:"webtvlogin",
                fromlist:"listuser",
                rememberme:""
            }, 
            success: function (data) {
                $(".getUserList").removeClass('not-active');
                mainselector.find('.listBanner').removeClass('card-loader card-loader--tabs');
                mainselector.find('.listBanner').removeAttr('style');
                if(typeof data == "string"){
                    data = JSON.parse(data);
                }
                if(data.result == 'success'){  
                    window.location.href = 'dashboard.php';
                }else{
                    showMsg("Invaild Username/Password","error");
                }
            }
        }); 
    });
    
    $(".deletelist").click(function(){
        custdxng = $(this).data("customindexing");
        usertodelete = $(".common-"+custdxng).data("anynameis");
        $("#usernamehere").html(usertodelete);
        $("#confirmdelete").data("userdelete",usertodelete);
        $("#confirmdeletelistuser").modal("show");
    });

    $("#confirmdelete").click(function(){
        userdelete = $(this).data("userdelete");

        $("#confirmdelete").prop("disabled",true);
        $("#confirmdelete").html("Processing..");
        selectrot = userdelete;
        if(localData != null && localData != "")
        {
            localStorage.removeItem("listUser");
            decoded2 = JSON.parse(localData);
            jQuery.each(decoded2, function(indexnew, itemnew) {               
               jQuery.each(itemnew, function(newanyname, newdataarr) {
                    if(selectrot != newanyname)
                    {
                        addTolistUser(itemnew); 
                    }                    
               });
            });
        }

        location.reload();
    });



/// Ends here

/// == Live Get Streams From Here == ///




});


function validateloginfields()
{
    $(".aborder").removeClass("aborder");
    totalInputs = $( ".inputBox" ).length;
    successcounter = 0;
    $( ".inputBox" ).each(function( index ) {
        if($(this).val() == "")
        {
            $(this).addClass("aborder");
        }
        else
        {
            successcounter = Number(successcounter)+Number(1);
        }
    });
    if(successcounter == totalInputs)
    {
        return "1";
    }
    else
    {
        return "0";
    }
}


function addTolistUser(newarray) {
    if (localStorage) {
        var listUser;
        if (!localStorage['listUser']) listUser = [];
        else listUser = JSON.parse(localStorage['listUser']);            
        if (!(listUser instanceof Array)) listUser = [];
        listUser.push(newarray);

        localStorage.setItem('listUser', JSON.stringify(listUser));
    } 
}

function showMsg(msg = "",iconis = "success"){
    // $('').html();
    Swal.fire({
        icon: iconis,
        title: msg,
        width: 600,
        padding: '3em !important',
        background: '#fff url(/images/trees.png)',
        backdrop: `
          rgba(0,0,123,0.4)
          url("/images/nyan-cat.gif")
          left top
          no-repeat
        `
      })
}




