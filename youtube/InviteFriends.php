<?php 

         $app_id = "166815126764719";

         $canvas_page = "http://cyberang3l.ath.cx:33335/youtube/";

         $message = "Would you like to join me in this great app?";

         $requests_url = "http://www.facebook.com/dialog/apprequests?app_id=" 
                . $app_id . "&redirect_uri=" . urlencode($canvas_page)
                . "&message=" . $message;

         if (empty($_REQUEST["request_ids"])) {
            echo("<script> top.location.href='" . $requests_url . "'</script>");
         } else {
            echo "Request Ids: ";
            print_r($_REQUEST["request_ids"]);
         }
?> 
