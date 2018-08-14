<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Webcomic reader (2015)</title>
  
  <link href="http://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
  <link rel="stylesheet" href="custom.css">
  <style>
    #comic_container {
      max-width: 100%;
      margin: 0 auto;
      text-align: center;
    }
    img#comic {
      max-width: 100%;
    }
    #comic_loading {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background: rgba(0,0,0,0.3);
    }
    #comic_loading div {
      position: fixed;
      top: 30%;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0px 0px 10px 0px rgba(50, 50, 50, 1);
      left:0;
      right:0;
      margin-left:auto;
      margin-right:auto;
      width: 170px;
    }
    #comic_pagenumber {
      position: fixed;
      top: 10px;
      left: 10px;
      padding: 5px;
      background-color: white;
      opacity: 0.8;
      border-radius: 10px;
      box-shadow: 0px 0px 7px 0px rgba(50, 50, 50, 0.8);
      font-size: small;
    }
    #comic_navigation {
      position: fixed;
      padding: 15px;
      background: white;
      opacity: 0.2;
      box-shadow: 0px 0px 10px 0px rgba(50, 50, 50, 1);
      left:0;
      right:0;
      margin-left:auto;
      margin-right:auto;
      bottom: 0;
    }
  </style>
  
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
    // -------------------------------------------------------------------------
    // DaVince's simple comic script
    // Powered by JavaScript & jQuery!
    // v0.4
    // 1 Jan 2015
    // -------------------------------------------------------------------------
    
    var MANGA_MODE = false; //set to true to flip the functionality of next and previous.
    var indicator_flashing = false;
    
    //Start of borrowed PHP
    <?php 
      // This bit of PHP was modified from:                                        //
      // Comic Gallery 1.2, Copyright (C) 2005 Stuart Robertson                    //
      // http://www.designmeme.com/                                                //
      // GPL 2.0 License: http://creativecommons.org/licenses/GPL/2.0/             //
      
      // Your images directory, relative to the page calling this script.
      $imagedir="images";
      
      // Don't edit the rest.
      $pics=array();
      $count=0;
      $comicdir=opendir($imagedir);
      
      // read directory into pics array
      while (($file = readdir($comicdir))!==false) {
        //  filter for jpg, gif or png files...   
        if (substr($file,-4) == ".jpg" || substr($file,-4) == ".jpeg" || substr($file,-4) == ".gif" || substr($file,-4) == ".png" || substr($file,-4) == ".JPG" || substr($file,-4) == ".JPEG" || substr($file,-4) == ".GIF" || substr($file,-4) == ".PNG") {
          $pics[$count] = $file;
          $count++;
        }
      }
      closedir($comicdir); 
      sort($pics); //sort the filenames alphabetically
      reset($pics);
    ?>
    //End of borrowed PHP
    
    // Get the pictures from the PHP array.
    <?php
      echo 'var pics = ["' . $imagedir . "/" . implode('", "' . $imagedir . "/", $pics) . '"];';
    ?>
  </script>
  
  
  
  <script>
    var curpage = 1;
    var notonhashchange = false;
    
    /** Document ready */
    $(document).ready(function() {
      if (window.location.hash.length > 0) //We have a #hash
        curpage = parseInt(window.location.hash.substring(1, window.location.hash.length)); //Get page number (like comicgallery-davince.php#3 for page 3)
      SwitchToPage(curpage-1); //-1 is because the array starts at 0, which is page 1 in the hash.
      
      //Switch comics when changing just the hash of the page. No page reloads needed!
      $(window).on('hashchange',function(){
        if (notonhashchange) { notonhashchange = false; return; }
        if (curpage == parseInt(window.location.hash.substring(1, window.location.hash.length))) return;
        curpage = parseInt(window.location.hash.substring(1, window.location.hash.length));
        SwitchToPage(curpage-1);
      });
      
      //Make tags with these classes act like this.
      $(".prevpage").click(function() { curpage--; SwitchToPage(curpage); })
      $(".nextpage").click(function() { curpage++; SwitchToPage(curpage); })
      $(".firstpage").click(function() { curpage = 0; SwitchToPage(curpage); })
      $(".lastpage").click(function() { curpage = pics.length-1; SwitchToPage(curpage); })
      
      //Shade/unshade page indicator
      $("#comic_pagenumber").mouseenter(function() { $(this).stop(true,true).fadeTo(200, 0.2) });
      $("#comic_pagenumber").mouseleave(function() { $(this).stop(true,true).fadeTo(200, 0.8) });
      
      //Navigation panel
      $("#comic_navigation").mouseleave(function() { $(this).stop(true,true).fadeTo(200, 0.2) });
      $("#comic_navigation").mouseenter(function() { $(this).stop(true,true).fadeTo(200, 0.9) });
      
      //Hide transparent navigation panel when past page
      $(window).scroll(function() {
          if($(window).scrollTop() + $(window).height() > $("#comic_page").offset().top+10) {
             $("#comic_navigation").hide();
          }
          else {
             $("#comic_navigation").show();
          }
      });
      
      //Keyboard input.
      $(window).on('keydown', function(e) {
        //console.log("key " + e.keyCode + " pressed");
        var prevkey = MANGA_MODE ? 39 : 37;
        var nextkey = MANGA_MODE ? 37 : 39;
        
        if (e.keyCode == prevkey) {
          //if (curpage == 0) return; //already on first page
          curpage--;
          SwitchToPage(curpage);
        }
        
        else if (e.keyCode == nextkey) {
          //if (curpage == pics.length-1) return; //already on last page
          curpage++;
          SwitchToPage(curpage);
        }
      });
    });
    
    /** Place the navigation menu. */
    function PutNavigation() {
      if (MANGA_MODE) {
        document.write(
          '<a class="lastpage" href="javascript:;"><< Last</a> ⋅ ' +
          '<a class="nextpage" href="javascript:;">< Next</a> ⋅ ' +
          '<a class="prevpage" href="javascript:;">Previous ></a> ⋅ ' +
          '<a class="firstpage" href="javascript:;">First >></a>');
      }
      else {
        document.write(
          '<a class="firstpage" href="javascript:;">|< First</a> ⋅ ' +
          '<a class="prevpage" href="javascript:;">< Previous</a> ⋅ ' +
          '<a class="nextpage" href="javascript:;">Next ></a> ⋅ ' +
          '<a class="lastpage" href="javascript:;">Last >></a>');
      }
    }
    
    /** Switch to the appropriate page. */
    function SwitchToPage(which) {
      if (pics.length == 0) return;
      
      //Go to first or last page in case of invalid numbers
      if (which < 0) {
        which = 0;
        FlashPageIndicator();
      }
      else if (which >= pics.length) {
        which = pics.length-1;
        FlashPageIndicator();
      }
      
      curpage = which;
      $("#comment").hide();
      
      //Load new image.
      var newimg = new Image();
      newimg.src = pics[which];
      notonhashchange = true;
      
      //If image was already loaded, just skip showing the loadi screen.
      if (newimg.complete) {
        ShowLoadedImage(which);
        return;
      }
      
      //Else, show it and fire handler.
      $("#comic_loading").fadeIn(50);
      newimg.onload = function() { ShowLoadedImage(which) };
    }
    
    /** Display the image after it has been loaded. */
    function ShowLoadedImage(which) {
      $('html, body').stop(true,true).animate({scrollTop:0}, 60, 'swing');
      $("#comic").attr("src", pics[which]);
      $("#comic_loading").fadeOut(0); //unlike hide(), fadeOut(0) will cancel any in-progress fadeIn
      $("#comic_pagenumber").html((which+1) + "/" + pics.length);
      
      window.location.hash = "#"+(curpage+1);
      $("#comment").html((comments[curpage+1]!=undefined)?comments[curpage+1]:"");
      $("#comment").show();
    }
    
    /** Flash the page indicator red when trying to go beyond the first/last page */
    function FlashPageIndicator() {
      if (indicator_flashing) return;
      indicator_flashing = true;
      $("#comic_pagenumber").css("background-color", "red");
      setTimeout(function() { $("#comic_pagenumber").css("background-color", "white"); }, 100);
      setTimeout(function() { $("#comic_pagenumber").css("background-color", "red"); }, 200);
      setTimeout(function() { $("#comic_pagenumber").css("background-color", "white"); }, 300);
      setTimeout(function() { indicator_flashing = false; }, 400);
    }
  </script>
  <script type="text/javascript" src="comic-comments.js"></script>
</head>

<body>
  <div id="comic_container">
    
    <!-- Loading message -->
    <div id="comic_loading">
      <div>Loading image...</div>
    </div>
    
    <!-- Page number -->
    <div id="comic_pagenumber"></div>
    
    <!-- Comic image -->
    <a class="nextpage" id="comic_page" href="javascript:;">
      <img id="comic" src="" alt="comic">
    </a>
    
    <!-- Comment -->
    <p id="comment"></p>
    
    <!-- First/Previous/Next/Last links -->
    <div id="comic_navigation">
      <script>PutNavigation();</script>
    </div>
    
    <div id="comic_bottom_navigation">
      <script>PutNavigation();</script>
    </div>
    
    <!-- List chapters. -->
    <h2>Chapters</h2>
    <p>
      <a href="#1">Chapter 1</a>
    </p>
    
  </div><!--/comic_container-->

</body>
</html>
