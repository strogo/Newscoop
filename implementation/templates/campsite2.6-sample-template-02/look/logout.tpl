<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Packaged Template #02</title>
<link rel="stylesheet" type="text/css" href="/look/style02.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body>

<table width="753" cellspacing="0" cellpadding="0" border="0">

  <!-- header -->

  <tr>
    <td colspan="5">
	  <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>

            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-left: 1px solid #06264F; border-right: 1px solid #06264F">
              <tr>
                <td background="/look/img/01.gif" style="padding: 3px 0px 3px 0px">
				  <!** include header.tpl>
				</td>
              </tr>
            </table>
			
			<!** include header-menu.tpl>
			
        </tr>
      </table>
	</td>
  </tr>
  
  <!-- end header -->
  
  <tr>
    <td width="130" valign="top" style="padding-top: 3px"> 
	
    <!-- leva kolona -->
    
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #10224A">
		
      <!-- search box -->

        <tr>
          <td bgcolor="#236EA9" align="center" style="padding-top: 9px; padding-bottom: 9px">
		    <!** include search-box.tpl>
          </td>
        </tr>
		
		<!-- banner -->
		
		<!-- menu -->
		
        <tr>
	      <td>
	        <!** include menu.tpl>
		  </td>
		</tr>
		
	  <!-- end menu -->
		
	  </table>             
<!** include left-banners.tpl>
    <!-- end leva kolona -->
	
	</td>
	<td width="10" bgcolor="#FFFFFF"></td>
	<td width="473" valign="top" style="padding-bottom:20px;">
	
    <!-- srednja kolona -->           
    
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
<META http-equiv="refresh" content="5;url=<!** uri issue>">
<p class="tekst">You have been logged out. Home page will be atuomaticly loaded. Please wait...<br><br>
If loading fail click <a class="plus" href="<!** uri template home.tpl>">here.</a>.


    <!-- end srednja kolona -->
	
	</td>
	<td width="10" bgcolor="#FFFFFF"></td>
    <td width="130" valign="top" bgcolor="#F0F0F0" style="padding-top: 12px">	

	<!-- desna kolona -->

	  <!** include right.tpl>
	
	<!-- end desna kolona -->
	
	</td>
  </tr>
 
  <!-- footer -->
 
  <tr>
    <td colspan="5">
	  <!** include footer.tpl>
	</td>
  </tr>
  
  <!-- end footer -->
  
</table>
</body>
</html>