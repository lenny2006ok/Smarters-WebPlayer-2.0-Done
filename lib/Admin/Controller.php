<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("ADMINCONTROLLERABSPATH", dirname(dirname(dirname(__FILE__))) . "/");
class Controller
{
    public function index($vars = [])
    {
        echo "control here";
    }
    public function loginheader($param = [])
    {
        if (file_exists("../admin/includes/header-login.php")) {
            include_once "../admin/includes/header-login.php";
        }
    }
    public function loginfooter($param = [])
    {
        if (file_exists("../admin/includes/footer-login.php")) {
            include_once "../admin/includes/footer-login.php";
        }
    }
    public function mainheader($param = [])
    {
        $titleis = isset($param["title"]) ? $param["title"] : "";
        $license = isset($param["license"]) ? $param["license"] : "";
        $logovalue = isset($param["logovalue"]) ? $param["logovalue"] : "";
        if (file_exists("../admin/includes/header.php")) {
            include_once "../admin/includes/header.php";
        }
    }
    public function mainsidebar($param = [])
    {
        $activemenu = isset($param["activemenu"]) ? $param["activemenu"] : "";
        $license = isset($param["license"]) ? $param["license"] : "";
        $logovalue = isset($param["logovalue"]) ? $param["logovalue"] : "";
        if (file_exists("../admin/includes/mainsidebar.php")) {
            include_once "../admin/includes/mainsidebar.php";
        }
    }
    public function mainfooter($param = [])
    {
        if (file_exists("../admin/includes/footer.php")) {
            include_once "../admin/includes/footer.php";
        }
    }
    public function loginscript($param = [])
    {
        echo "           <script type=\"text/javascript\">\n               \$(document).ready(function(){\n                  \$(\".loginprogress\").click(function(e){\n                    e.preventDefault();\n                    \$(\".error-span\").addClass(\"d-none\");\n                    \$(\".addrequiredborder\").removeClass(\"addrequiredborder\");\n                    successcounter = 0;\n                    UsernameValue = \$(\"#usernameselector\").val();\n                    PasswordValue = \$(\"#passwordselector\").val();\n                    if(UsernameValue != \"\")\n                    {\n                        successcounter = Number(successcounter)+Number(1);\n                    }\n                    else\n                    {\n                        \$(\"#usernameselector\").addClass(\"addrequiredborder\");\n                    }\n                    if(PasswordValue != \"\")\n                    {\n                        successcounter = Number(successcounter)+Number(1);\n                    }\n                    else\n                    {\n                        \$(\"#passwordselector\").addClass(\"addrequiredborder\");\n                    }\n\n\n                    checkreacptha = \"\";\n                    captharesponse = \"\";\n                    if(\$(\"#g-recaptcha-response\").length > 0)\n                    {\n                      checkreacptha = \"yes\";\n                      captharesponse = grecaptcha.getResponse();\n                      if(captharesponse == \"\")\n                      {\n                        \$(\".g-recaptcha\").addClass(\"addrequiredborder\");\n                        successcounter = Number(successcounter)-Number(1);\n                      }\n                    }\n\n\n                    if(successcounter == 2)\n                    {\n\n\n                        \n\n\n                        Rememberme = \"\";\n                        if(\$('#rememberme').prop('checked'))\n                        {\n                          Rememberme = \"on\";\n                        }\n                        \$(\".loginprogress\").prop('disabled', true);\n                        \$(\".loginprogress\").text('SIGN IN PROCESS');\n                         jQuery.ajax({\n                          type:\"POST\",\n                          url:\"includes/ajax-control.php\",\n                          dataType:\"text\",\n                          data:{\n                          action:'loginadminprocess',\n                          username:UsernameValue,\n                          password:PasswordValue,\n                          checkreacptha:checkreacptha,\n                          captharesponse:captharesponse,\n                          rememberme:Rememberme\n                          },  \n                            success:function(response2){\n                              \$(\".loginprogress\").prop('disabled', false);\n                              \$(\".loginprogress\").html('<i class=\"fa fa-sign-in fa-lg fa-fw\"></i> SIGN IN');\n                                var responseObj = jQuery.parseJSON(response2);\n                                if(responseObj.result == \"success\")\n                                {\n                                    window.location.href = 'dashboard.php';\n                                }\n                                else if(responseObj.result == \"verify2fa\")\n                                {\n\n                                    htmlfortwofa = '<div class=\"login-form\">';\n                                    htmlfortwofa+= '<h3 class=\"login-head\"><i class=\"fa fa-lg fa-fw fa-user\"></i>SIGN IN</h3>';\n                                    htmlfortwofa+= '<div class=\"form-group\">';\n                                    htmlfortwofa+= '<label class=\"control-label\">2FA CODE *</label>';\n                                    htmlfortwofa+= '<input class=\"form-control\" type=\"text\" id=\"twofacodeinput\" placeholder=\"Enter 2FA Code\" value=\"\">';\n                                    htmlfortwofa+= '</div>';\n                                    htmlfortwofa+= '<div class=\"form-group btn-container\">';\n                                    htmlfortwofa+= '<button type=\"button\" class=\"btn btn-primary btn-block\" id=\"validatetwocode\">Validate Code</button>';\n                                    htmlfortwofa+= '</div>';\n                                    htmlfortwofa+= '</div>';\n                                    \$(\".login-box\").css(\"min-height\",\"300px\");\n                                    \$(\".login-box\").html(\"\");\n                                    \$(\".login-box\").html(htmlfortwofa);\n\n\n                                    \$(document).keypress(function(e) {\n                                          if(e.which == '13'){\n                                            verifytwofacodefunction(UsernameValue,PasswordValue,Rememberme);\n                                          }\n                                      }); \n\n                                    \$( \"#validatetwocode\" ).bind( \"click\", function(e) {\n                                        e.preventDefault();\n                                        verifytwofacodefunction(UsernameValue,PasswordValue,Rememberme);\n                                    });\n\n                                    function verifytwofacodefunction(UsernameValue = \"\",PasswordValue = \"\",Rememberme = \"\")\n                                    {\n                                        if(\$(\"#twofacodeinput\").val() != \"\")\n                                        {\n                                            \$( \"#validatetwocode\" ).prop('disabled', true); \n                                            \$( \"#validatetwocode\" ).text(\"Processing..\"); \n                                            jQuery.ajax({\n                                                type:\"POST\",\n                                                url:\"includes/ajax-control.php\",\n                                                dataType:\"text\",\n                                                data:{\n                                                action:'loginadminprocess',\n                                                username:UsernameValue,\n                                                password:PasswordValue,\n                                                rememberme:Rememberme,\n                                                validatetwofacode:\$(\"#twofacodeinput\").val()\n                                                },\n                                                success:function(responsetwofa){\n                                                  \$( \"#validatetwocode\" ).text(\"Validate Code\"); \n                                                  \$( \"#validatetwocode\" ).prop('disabled', false); \n                                                  var responsetwofa = jQuery.parseJSON(responsetwofa);\n                                                  if(responsetwofa.result == \"success\")\n                                                  {\n                                                      window.location.href = 'dashboard.php';\n                                                  }\n                                                  else\n                                                  {\n                                                      Swal.fire({\n                                                        position: 'center',\n                                                        type: 'error',\n                                                        title: responsetwofa.message,\n                                                        showConfirmButton: false,\n                                                        timer: 2000\n                                                      })\n                                                  }\n                                                }\n                                              }); \n\n                                        }\n                                        else\n                                        {\n                                             \$(\"#twofacodeinput\").addClass(\"addrequiredborder\");\n                                        }\n                                    }\n\n                                }\n                                else\n                                {\n                                   \n                                    if(responseObj.blocked == \"yes\")\n                                    {\n                                        Swal.fire({\n                                          position: 'center',\n                                          type: 'error',\n                                          title: responseObj.message,\n                                          showConfirmButton: false,\n                                          timer: 2500\n                                        })\n                                        window.setTimeout(function(){\n                                          window.location.href = 'blocked.php';\n                                        } ,2500);\n                                    }\n                                    else\n                                    {\n                                       Swal.fire({\n                                        position: 'center',\n                                        type: 'error',\n                                        title: responseObj.message,\n                                        showConfirmButton: false,\n                                        timer: 2000\n                                      })\n                                    }\n                                }\n                            }\n                          }); \n\n                      \n                    }\n                    else\n                    {\n                      Swal.fire({\n                        position: 'center',\n                        type: 'error',\n                        title: 'Please Fill Required Fields!!',\n                        showConfirmButton: false,\n                        timer: 1500\n                      })\n                    }\n                  });\n\n                  \$( \"#usernameselector\" ).focus(function() {\n                      \$(\"#usernameselector\").removeClass(\"addrequiredborder\");\n                  });\n\n                  \$( \"#passwordselector\" ).focus(function() {\n                      \$(\"#passwordselector\").removeClass(\"addrequiredborder\");\n                  });\n               });\n           </script>\n           ";
    }
    public function checkadminlogin($param = [])
    {
        $return = "";
        if (isset($_SESSION["webadmin"]) && $_SESSION["webadmin"] != "") {
            $return = "1";
        }
        return $return;
    }
    public function createallrecommendedtables($conn = [])
    {
        if (file_exists(ADMINCONTROLLERABSPATH . "admin/includes/functions.php")) {
            include_once ADMINCONTROLLERABSPATH . "admin/includes/functions.php";
            $controlfunctions = new controlfunctions();
            $CreateRecommendedFunctions = $controlfunctions->createallrecommendedtablesfunction($conn);
        }
    }
}

?>