<?php
/********************************************
* For pagination in showtweets.php

	For More Detail please Visit: 
	
	http://www.discussdesk.com/download-pagination-in-php-and-mysql-with-example.htm

	************************************************/

   function displayPaginationBelow($per_page,$page,$keywords,$searchKey,$total){
	   $encodeKeyword = urlencode($keywords);
	   $serckquery = ''; 
	   if($searchKey != ''){
		  $serckquery = '&search_keywords='.$searchKey; 
	   }
	   $page_url="?";
        $adjacents = "2"; 
    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;
    	$prev = $page - 1;							
    	$next = $page + 1;
        $setLastpage = ceil($total/$per_page);
    	$lpm1 = $setLastpage - 1;
    
    	$setPaginate = "";
    	if($setLastpage > 1)
    	{	
    		$setPaginate .= "<ul class='setPaginate'>";
                    $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
    		if ($setLastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $setLastpage; $counter++)
    			{
    				if ($counter == $page)
    					$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
    				else
    					$setPaginate.= "<li><a href='{$page_url}page=$counter&keyword={$encodeKeyword}{$serckquery}'>$counter</a></li>";					
    			}
    		}
    		elseif($setLastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
    					else
						{
							$setPaginate.= "<li><a href='{$page_url}page=$counter&keyword={$encodeKeyword}{$serckquery}'>$counter</a></li>";
						}
    				}
    				$setPaginate.= "<li class='dot'>...</li>";
    				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&keyword={$encodeKeyword}{$serckquery}'>$lpm1</a></li>";
    				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&keyword={$encodeKeyword}{$serckquery}'>$setLastpage</a></li>";		
    			}
    			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$setPaginate.= "<li><a href='{$page_url}page=1&keyword={$encodeKeyword}{$serckquery}'>1</a></li>";
    				$setPaginate.= "<li><a href='{$page_url}page=2&keyword={$encodeKeyword}{$serckquery}'>2</a></li>";
    				$setPaginate.= "<li class='dot'>...</li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
    					else
    						$setPaginate.= "<li><a href='{$page_url}page=$counter&keyword={$encodeKeyword}{$serckquery}'>$counter</a></li>";					
    				}
    				$setPaginate.= "<li class='dot'>..</li>";
    				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&keyword={$encodeKeyword}{$serckquery}'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&keyword={$encodeKeyword}{$serckquery}'>$setLastpage</a></li>";		
    			}
    			else
    			{
					$setPaginate.= "<li><a href='{$page_url}page=1&keyword={$encodeKeyword}{$serckquery}'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=2&keyword={$encodeKeyword}{$serckquery}'>2</a></li>";
    				$setPaginate.= "<li class='dot'>..</li>";
    				for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
    				{
    					if ($counter == $page)
    						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
    					else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&keyword={$encodeKeyword}{$serckquery}'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$setPaginate.= "<li><a href='{$page_url}page=$next&keyword={$encodeKeyword}{$serckquery}'>Next</a></li>";
                $setPaginate.= "<li><a href='{$page_url}page=$setLastpage&keyword={$encodeKeyword}{$serckquery}'>Last</a></li>";
    		}else{
    			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
                $setPaginate.= "<li><a class='current_page'>Last</a></li>";
            }

    		$setPaginate.= "</ul>\n";		
    	}
    
    
        return $setPaginate;
    } 
?>