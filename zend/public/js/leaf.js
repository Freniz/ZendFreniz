
	 
// This is the first thing we add ------------------------------------------
	    $(document).ready(function() {
	    
	        $('.rate_widget').each(function(i) {
	            var widget = this;
	            var out_data = {
	                widget_id : $(widget).attr('id'),
	                fetch: 1
	            };
	          
	            $.post(
	                'http://localhost:10088/freniz_zend/public/ratings.php',
	                out_data,
	                function(INFO) {
	                    $(widget).data( 'fsr', INFO );
	                    set_votes(widget);
	                },
	                'json'
	            );
	        });
	    
	
	        $('.ratings_stars').hover(
	            // Handles the mouseover
	            function() {
	                $(this).prevAll().andSelf().addClass('ratings_over');
	                $(this).nextAll().removeClass('ratings_vote'); 
	            },
	            // Handles the mouseout
	            function() {
	                $(this).prevAll().andSelf().removeClass('ratings_over');
	                // can't use 'this' because it wont contain the updated data
	                set_votes($(this).parent());
	            }
	        );
	        
	        
	        // This actually records the vote
	        $('.ratings_stars').bind('click', function() {
	            var star = this;
	            var widget = $(this).parent();
	           
	            
	            var clicked_data = {
	                clicked_on : $(star).attr('class'),
	                widget_id : $(star).parent().attr('id')
	            };
	            $.post(
	                'http://localhost:10088/freniz_zend/public/ratings.php',
	                clicked_data,
	                function(INFO) {
	                    widget.data( 'fsr', INFO );
	                    set_votes(widget);
	                },
	                'json'
	            ); 
	        });
	        
	        
	    	
	    	

	    	
	    	$(".moody").live('click',function(){
	    		  $("#mood-set-div").css({"display":"block"});
                     $a='<ul class="mood"style="position:absolute"><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/6smiley_face.gif"onclick="changemood(\'6smiley_face.gif.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/130.png"onclick="changemood(\'130.png\');document.getElementById(\'mood-smile\').style.display=\'non\'"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/22461291.png"onclick="changemood(\'22461291.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/angel.png"onclick="changemood(\'angel.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/att.png"onclick="changemood(\'att.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/att2.png"onclick="changemood(\'att2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/cas.png"onclick="changemood(\'cas.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/cry.gif"onclick="changemood(\'2cry.gif\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/cry1.png"onclick="changemood(\'cry1.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/cry2.png"onclick="changemood(\'cry2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/cry3.png"onclick="changemood(\'cry3.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/irritate.png"onclick="changemood(\'irritate.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/kiss.png"onclick="changemood(\'kiss.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love.png"onclick="changemood(\'love.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love2.png"onclick="changemood(\'love2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love3.png"onclick="changemood(\'love3.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love4.png"onclick="changemood(\'love4.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love5.png"onclick="changemood(\'love5.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/love.6png.png"onclick="changemood(\'love.6png.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/nospeak.png"onclick="changemood(\'nospeak.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/prirate.png"onclick="changemood(\'prirate.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/resign.png"onclick="changemood(\'resign.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad.png"onclick="changemood(\'sad.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad1.png"onclick="changemood(\'sad1.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad2.png"onclick="changemood(\'sad2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad3.png"onclick="changemood(\'sad3.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad4.png"onclick="changemood(\'sad4.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sad5.png"onclick="changemood(\'sad5.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/Sad06.gif"onclick="changemood(\'Sad06.gif\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/shut.png"onclick="changemood(\'shut.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/shut1.png"onclick="changemood(\'shut1.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sleep.png"onclick="changemood(\'sleep.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sleep2.png"onclick="changemood(\'sleep2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm2.png"onclick="changemood(\'sm2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm3.png"onclick="changemood(\'sm3.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm4.png"onclick="changemood(\'sm4.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm5.png"onclick="changemood(\'sm5.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm6.png"onclick="changemood(\'sm6.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm7.png"onclick="changemood(\'sm7.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/happy.png"onclick="changemood(\'happy.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/sm9.png"onclick="changemood(\'sm9.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/smile.png"onclick="changemood(\'smile.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/smile2.png"onclick="changemood(\'smile2.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/smile3.png"onclick="changemood(\'smile3.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/SmileyCoffeeTired.jpg"onclick="changemood(\'SmileyCoffeeTired.jpg\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/smiley-sad.png"onclick="changemood(\'smiley-sad.png\');"/></li><li><img src="http://localhost:10088/freniz_zend/public/images/mood/32/stop.png"onclick="changemood(\'stop.png\');"/></li></ul><div style="width:320px; display:none; position:absolute; margin-top:160px; margin-left:10px; float:left; padding:5px; background-color:#ccc; border:solid 1px"><div style="width:32px; float:left;  "><img id="mood-image" src="" alt="" height="32"width="32"/></div><div style="width:220px; padding-left:5px; margin-left:5px; float:left;  border-left:solid 1px #fff"><textarea id="mood-description" style="width:220px; outline:none; height:35px"placeholder="how you feel about today?"></textarea></div><input class="greenbutton" style="float:right; margin-top:7px" type="button" onclick="updatemood()" value="update"/></div>';   
                     $('span-smiley').html($a);
                     $('.mood').css('display','block');
	                $("span-smiley").css("display","block");
	    			$("span-smiley").css("position","absolute");
	    			$('.edit-cancel-span').css('display','block');
	    		  
	    			
	    		});
	    	$('#mood-edit').live('click',function(){
	    		$("#mood-set-div").css({"display":"block"});
    			$('.edit-cancel-span').css('display','block');
	    		 $a='<div style="width:320px; display:none; position:absolute; margin-top:160px; margin-left:13px; float:left; padding:5px; background-color:#ccc; border:solid 1px"><div style="width:32px; float:left;  "><img id="mood-image" src="" alt="" height="32"width="32"/></div><div style="width:220px; padding-left:5px; margin-left:5px; float:left;  border-left:solid 1px #fff"><textarea id="mood-description" style="width:220px; outline:none; height:35px"placeholder="how you feel about today?"></textarea></div><input class="greenbutton" style="float:right; margin-top:7px" type="button" onclick="updatemood()" value="update"/></div>';   
                  $('span-smiley').html($a);
	    		  $("span-smiley").css("display","block");
	    		  $("span-smiley").css("position","absolute");
	    		$('.mood').css('display','none');
	    		$('#mood-smile div').css('display','block');
	    		$('#mood-image').attr('alt',$('#smileypic').attr('alt'));
	    		$('#mood-image').attr('src',$('#smileypic').attr('src'));
	    		$('#mood-description').val($('#mood-desc').html());
	    		$("#mood-set-div").css("background","none");
	    		
	    	});
	    		$('.edit-cancel-span').click(function(){
	    		$("span-smiley").css("display","none"); $('.edit-cancel-span').css('display','none');$("#mood-set-div").css("display","none"); $('#mood-smile div').css('display','none');
	    		});
	    		$("span-smiley").mouseover(function(){
	    			$("span-smiley").css("display","block");
	    			
	    		});
	    		
	    		$(".moody").mouseover(function(){
	    			$("span-edit").css("display","block");
	    			
	    		});
	    		$(".moody").mouseout(function(){
	    			$("span-edit").css("display","none");
	    			
	    		});
	    		$("#secondarypic1").mouseover(function(){
	    			$(".span-edit-sec").css("display","block");
	    			
	    		});
	    		$("#secondarypic1").mouseout(function(){
	    			$(".span-edit-sec").css("display","none");
	    			
	    		});
	    	
	    		  $('#acount-trigger-setting').click(function(){
	    		        
		                $('#acount-trigger-setting').next('#acount-content-setting').slideToggle();
							$('#acount-trigger-setting').addClass('active');
		                                     
		                                     
							if ($('#acount-trigger-setting').hasClass('active')) $('#acount-trigger-setting').find('span').html('&#x25BC;');
								else $('#acount-trigger-setting').find('span').html('&#x25B2;') ; 
		                                     
		                                     
							});
			 
		                               $(document).click(function(e){
		                                               
		                    if($(e.target).parents().index($('#settings')) == -1) {
		                        if($('#acount-content-setting').is(":visible")) {
		                            if (!$('#acount-content-trigger').hasClass('active')){ 
		                                 $('#acount-trigger-setting').next('#acount-content-setting').slideToggle();
		                                                }
		                            
		                        }
		                    }        
		               
		                                        });	
	        
	        
	    });
	
	    function set_votes(widget) {
	
	        var avg = $(widget).data('fsr').whole_avg;
	        var votes = $(widget).data('fsr').number_votes;
	        var exact = $(widget).data('fsr').dec_avg;
	    
	        window.console && console.log('and now in set_votes, it thinks the fsr is ' + $(widget).data('fsr').number_votes);
	        
	        $(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
	        $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
	        $(widget).find('.total_votes').text( votes + ' votes recorded (' + exact + ' rating)' );
	    }
	    // END FIRST THING


	    
        $('#blog-button').click(function(){
           
 
$b='<div id="light" class="white_content"><div style="width:500px; height:200px; margin-left:20px; margin-top:20px; "><form name="blogmessage" onsubmit="createblogstatus()"><div style="width:300px; height:20px; margin-top:10px; margin-left:60px; float:left;"><input type="text" id="blg_title" size="40" /></div><div style="width:300px; height:100px; margin-top:10px; margin-left:60px; float:left;"><textarea rows="4" cols="50" id="blg" ></textarea></div><div style="width:300px; height:20px; margin-top:10px; margin-left:60px; float:left;"><input type="file" id="blg_url" value="Upload" size="40" /></div><div style="width:300px;  margin-left:40px;"><ul class="roundbuttons sendmessagewidth" style="margin-left:40px;"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById(\'light\').style.display=\'none\';   document.getElementById(\'fade\').style.display=\'none\';"  /></li><li><input type="button" name="send" value="send" onclick="createblogstatus()" /></li></ul></div></form></div></div>';

    $('#normal').html($b); 
             $('#light').css({'display':'block'});
             $('#fade').css({'display':'block'});
            
        });
        
          $('#admire-button').click(function(){
           
$c='<div id="light" class="white_content"><div style="width:500px; height:200px; margin-left:20px; margin-top:20px; "><form name="admiremess"onsubmit="createadmirestatus()"><div style="width:300px; height:100px; margin-top:10px; margin-left:60px; float:left;"><textarea rows="4"cols="50"name="admr"></textarea></div><div style="width:300px; "><ul class="roundbuttons sendmessagewidth"><li><input type="button"name="admire-cancel"value="cancel"onClick="document.getElementById(\'light\').style.display=\'none\';   document.getElementById(\'fade\').style.display=\'none\';"/></li><li><input type="button"name="admire-send"value="send"onclick="createadmirestatus()"/></li></ul></div></form></div></div>';

    $('#normal').html($c); 
             $('#light').css({'display':'block'});
             $('#fade').css({'display':'block'});
            
        });
           $('#video-button').click(function(){
           
$d='<div id="light" class="white_content"><div style="width:500px; height:200px; margin-left:20px; margin-top:20px; "><form name="video-upd"onsubmit="addvideos()"><div style="width:300px; height:100px; margin-top:10px; margin-left:60px; float:left;"><textarea rows="4"cols="50"name="video-addr"></textarea></div><div style="width:300px; "><ul class="roundbuttons sendmessagewidth"><li><input type="button"name="video-cancel"value="cancel"onClick="document.getElementById(\'light\').style.display=\'none\';   document.getElementById(\'fade\').style.display=\'none\';"/></li><li><input type="button"name="video-send"value="send"onclick="addvideos()"/></li></ul></div></form></div></div>';

    $('#normal').html($d); 
             $('#light').css({'display':'block'});
             $('#fade').css({'display':'block'});
            
        });
	
    
	    