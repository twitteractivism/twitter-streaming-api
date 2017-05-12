/**
* site.js
* Manage the UI for the tweet display
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/

// Use jQuery to simplify the programming
// All Javascript programmers should learn jQuery!
jQuery(document).ready(function($){
  $.ajaxSetup({
    cache: false
  });

  /***** Initial page load *****/
  
  // Keep checking the new tweet count based on the refresh value
  var new_count_refresh = $("#new_count_refresh").html() * 1000;
  setInterval(get_new_tweet_count, new_count_refresh);
  
  // Reload the page when the count is clicked
  $("#new_tweet_count").click(function(){
    window.location.reload();
  });
  
  // Set up the more tweets button at the bottom of the list
  // Get the current number of tweets displayed
  var tweet_count = $("#tweet_list").find(".tweet").length;
  
  // If there are tweets displayed
  if (tweet_count) {
    // Fill in the count and display the button 
    $("#tweet_count").html(tweet_count);
    $("#more_tweets_button").show();	
    
    // Get older tweets when the button is clicked
    $("#more_tweets_button").click(function(){
      load_more_tweets();
    });
  }
  /**********/	
  
  // Refresh the new tweet count at the top of the list 
  function get_new_tweet_count(){  
        
    // Get the tweet_id of the tweet at the top of the list
    var first_id = $(".tweet_id").first().html();
    
    // The server's URL was stored in the ajax_url div when the page was generated
    // Use Ajax to get a count of newer tweets from the server
    
    $.get($("#ajax_url").html() + 
      "plugins/twitter_display/get_new_tweet_count.php?first=" + first_id, 
      function(count_html) {
  			
        // If new tweets were found, display the new count	
        if (count_html != ' ') {
          $("#new_tweet_count").show().html(count_html);
        }
    });
  }
  
  // Get older tweets from the server when the more tweets button is clicked
  function load_more_tweets() {
    
    // Disable the button, so it can't be clicked multiple times in a row
    $('#more_tweets_button').unbind('click');
    
    // Save the button's current values, and display a progress GIF
    var old_button = $("#more_tweets_button").html();
    var old_count = $("#tweet_list").find(".tweet").length;
    $("#more_tweets_button").html("<center><img src='" +
      $("#ajax_url").html() + 
      "plugins/twitter_display/ajax-loading.gif'></center>");
  		
    // Ask the server for tweets older than the one at the bottom of the list
    var last_id = $(".tweet_id").last().html();
    $.get( $("#ajax_url").html() + 
      "plugins/twitter_display/get_tweet_list.php?last=" + last_id, 
      function(tweet_html){
  
      // Add the server's response to the end of the list
      $("#tweet_list").append(tweet_html);
  
      // If more tweets were added, redisplay the button with the new count
      var new_count = $("#tweet_list").find(".tweet").length;
      if (new_count > old_count) {
        $("#more_tweets_button").html(old_button);
        $("#tweet_count").html(new_count);
        
        // Enable the click event again
        $("#more_tweets_button").click(function(){
          load_more_tweets();
        });
      } else {
        // No older tweets are available
        $("#more_tweets_button").hide();
      }
    });
  }
});