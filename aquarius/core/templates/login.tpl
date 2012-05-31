<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <title>aquarius cms</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/admin.css" type="text/css" />
    <link rel="shortcut icon" href="./favicon.ico" />
    <link rel="shortcut icon" type="image/png" href="./favicon.png" />
    <style type="text/css" media="all">{literal}html, body {height: 95%} body {max-width: none;} #lofe { margin-left: 5px; margin-top: -15px;}{/literal}</style>
    <!--[if IE]>
        <style type="text/css" media="all">{literal}#lofe {width: 311px; margin-left: -15px;}{/literal}</style>
    <![endif]-->
    <script language="javascript" type="text/javascript">{literal}
    <!--
        function initLogin() {
            if (top != self) top.location = self.location;
            document.login.username.focus();
        }
    // -->
    {/literal}</script>
</head>
<body onload="initLogin()" id="loginpage">
<table width="100%" height="95%" border="0" cellspacing="0" cellpadding="0" id="login">
    <tr>
        <td align="center" valign="middle">
            <div id="loginhead">
                <img src="picts/logologin2.gif" alt="aquarius cms" id="loginlogo" />
            </div>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="login-outer">
                <tr>
                    <td valign="middle" align="center">
                        <form action="" method="post" name="login" enctype="multipart/form-data">
                            <table border="0" width="350" cellpadding="0" id="login-inner">
                                <tr>
                                    <td valign="middle" width="80" class="login-firstrow">Username</td>
                                    <td align="right" class="login-firstrow"><input type="text" name="username" style="width:220px; margin:0; margin-right:10px" maxlength="160" class="ef" /></td>
                                </tr>
                                <tr>
                                    <td valign="middle" width="80">Password</td>
                                    <td align="right">
                                        <input type="password" name="password" style="width:220px; margin:0; margin-right:10px" maxlength="160" class="ef" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="80">&nbsp;</td>
                                    <td>&nbsp;
                                        <input type="submit" value="Login" name="backend_login" class="submit" style="margin:0; margin-bottom: 5px; margin-right:10px; float:right" />
                                        <div id="lofe"><input type="checkbox" name="login_frontend" id="login_frontend" /> <label for="login_frontend" style="font-size:11px; color:grey; display:inline;">{#login_frontend#}</label></div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
            <p id="loginfooter">
                 aquarius cms v.{$smarty.const.AQUARIUS_VERSION} | 
                &copy; <a href="http://www.aquaverde.ch/" target="_blank" style="color:#ccc">aquaverde.ch</a>
               
            </p>
        </td>
    </tr>
</table>
</body>
</html>